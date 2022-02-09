<?php


namespace CodaImporter\Http\Web\Controller;


use CodaImporter\Application\Coda\Command\CodaHandlerCommandInterface;
use CodaImporter\Application\Coda\Command\CodaHandlerFactory;
use CodaImporter\Application\Coda\Query\CodaFileParserQueryHandler;
use CodaImporter\Application\Coda\Query\MatcherQuery;
use CodaImporter\Application\Coda\Query\MatcherQueryHandler;
use CodaImporter\Application\Coda\ViewModel\AccountancyFile;
use CodaImporter\Application\Coda\ViewModel\AccountancyFileType;
use CodaImporter\Application\Coda\ViewModel\MatchResult;
use CodaImporter\Application\Coda\ViewModel\Statements;
use CodaImporter\Http\Web\Form\ImportForm;
use CodaImporter\Http\Web\Form\ImportFormStepOne;
use CodaImporter\Infrastructure\Coda\Repository\Query\AccountancyFilesQueryRepository;
use Paiement;

final class ImportController extends WebController
{
    const SEQUENCE_DELIMITER = '_';

    /**
     * @var \Conf|\stdClass
     */
    private $conf;

    /**
     * @var CodaFileParserQueryHandler
     */
    private $codaParserQuery;

    public function __construct(\DoliDB $db)
    {
        global $conf;
        parent::__construct($db);

        $this->conf = $conf;
    }

    public function getAction()
    {
        $form = new ImportForm('import_coda', $this->db);
        return $this->render('import/form.phtml', [
            'codaForm' => $form
        ]);
    }

    public function stepOneAction()
    {
        if (!$this->request->isPost()) {
            return $this->redirect($_SERVER["PHP_SELF"] . '?r=import');
        }

        $form = new ImportForm('import_coda', $this->db);
        if ($form->validate()) {
            if (dol_add_file_process($this->conf->codaimporter->dir_output . '/codas', 1, 0, 'coda') > 0) {
                $name = $this->fileName($_FILES['coda']['name']);

                $formOne = new ImportFormStepOne('import_coda');
                $formOne->setAction('?r=importStepTwo');
                $formOne->setData([
                    'coda' => $_FILES['coda']['name'],
                ]);

                return $this->render('import/step1.phtml', [
                    'statements' => $this->parseFile($name),
                    'codaForm' => $formOne,
                ]);
            }
        }
    }

    public function stepTwoAction()
    {
        if (!$this->request->isPost()) {
            return $this->redirect($_SERVER["PHP_SELF"] . '?r=import');
        }

        $data = $this->request->getPostParameters();

        if (!isset($data['coda'])) {
            return $this->redirect($_SERVER["PHP_SELF"] . '?r=import');
        }

        $statement = $this->parseFile($this->conf->codaimporter->dir_output . '/codas/' . $data['coda']);
        $accountingFiles = $this->getAccountingFiles();

        return $this->render('import/step2.phtml', [
            'matches' => $this->match($statement, $accountingFiles),
            'coda' => $data['coda'],
        ]);

    }

    public function stepThreeAction()
    {
        if (!$this->request->isPost()) {
            return $this->redirect($_SERVER["PHP_SELF"] . '?r=import');
        }

        if (!$this->request->hasParameter('matching')) {
            return $this->redirect($_SERVER["PHP_SELF"] . '?r=import');
        }

        $data = $this->request->getPostParameters();
        if (!isset($data['coda'])) {
            return $this->redirect($_SERVER["PHP_SELF"] . '?r=import');
        }

        $statements = $this->parseFile($this->conf->codaimporter->dir_output . '/codas/' . $data['coda']);
        foreach ($this->request->getParam('matching') as $sequence => $fileKey) {

            $statementSequence = $this->extractStatementSequence($sequence);
            $transactionSequence = $this->extractTransactionSequence($sequence);
            $transaction = $statements->findTransaction($statementSequence, $transactionSequence);

            if (empty($fileKey)) {
                continue; //TODO add a warning / an info, the select wasn't filled in
            }

            if (null === $transaction) {
                continue; //TODO add a warning/ an info the transaction has not been found.
            }

            $fileType = $this->extractFileType($fileKey);
            $fileId = $this->extractFileId($fileKey);

            $this->createCodaHandler($fileType)->handle($transaction, $fileId, 5);
        }

    }

    /**
     * @param string $file
     *
     * @return Statements
     */
    private function parseFile($file)
    {
        return $this->getFileParser()->query($file);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function fileName($name)
    {
        return $this->conf->codaimporter->dir_output . '/codas/' . $name;
    }

    /**
     * @return array|AccountancyFile[]
     *
     * @throws \Exception
     */
    private function getAccountingFiles()
    {
        $queryHandler = new AccountancyFilesQueryRepository($this->db);
        return $queryHandler->query([]);
    }

    /**
     * @param Statements $statements
     * @param array|AccountancyFile[] $files
     *
     * @return array|MatchResult[]
     */
    private function match(Statements $statements, array $files)
    {
        $handler = new MatcherQueryHandler();

        $result = [];
        foreach ($statements->transactions() as $transaction) {
            $result[] = $handler->query(new MatcherQuery($transaction, $files));
        }

        return $result;
    }

    /**
     * @return CodaFileParserQueryHandler
     */
    private function getFileParser(): CodaFileParserQueryHandler
    {
        if (null === $this->codaParserQuery) {
            $this->codaParserQuery = new CodaFileParserQueryHandler();
        }

        return $this->codaParserQuery;
    }

    private function extractTransactionSequence(string $sequence)
    {
        $sequences = explode(self::SEQUENCE_DELIMITER, $sequence);
        return (int)$sequences[1];
    }

    private function extractStatementSequence(string $sequence)
    {
        $sequences = explode(self::SEQUENCE_DELIMITER, $sequence);
        return (int)$sequences[0];
    }

    private function createCodaHandler(string $fileType): CodaHandlerCommandInterface
    {
        return CodaHandlerFactory::instance($this->db, $fileType);
    }

    private function extractFileType(string $fileKey): string
    {
        if (empty($fileKey)) {
            return '';
        }

        if (strpos($fileKey, AccountancyFileType::BILL) !== false) {
            return AccountancyFileType::BILL;
        }

        if (strpos($fileKey, AccountancyFileType::EXPENSE_REPORT) !== false) {
            return AccountancyFileType::EXPENSE_REPORT;
        }

        if (strpos($fileKey, AccountancyFileType::BILL_SUPPLIER) !== false) {
            return AccountancyFileType::BILL_SUPPLIER;
        }

        return explode(self::SEQUENCE_DELIMITER, $fileKey)[0];
    }

    private function extractFileId(string $fileKey): int
    {
        if (empty($fileKey)) {
            throw new \InvalidArgumentException('File key must be defined');
        }

        $splitted = explode(self::SEQUENCE_DELIMITER, $fileKey);
        return (int)($splitted[(count($splitted)-1)]);
    }

}