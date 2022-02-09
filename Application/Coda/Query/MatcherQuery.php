<?php


namespace CodaImporter\Application\Coda\Query;


use CodaImporter\Application\Coda\ViewModel\AccountancyFile;
use Codelicious\Coda\Statements\Statement;
use Codelicious\Coda\Statements\Transaction;

final class MatcherQuery
{
    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var AccountancyFile[]
     */
    private $files;

    /**
     * @param Transaction $transaction
     * @param AccountancyFile[] $files
     */
    public function __construct(Transaction $transaction, array $files)
    {
        $this->transaction = $transaction;
        $this->files = $files;
    }

    /**
     * @return Transaction
     */
    public function transaction()
    {
        return $this->transaction;
    }

    /**
     * @return array|AccountancyFile[]
     */
    public function files()
    {
        return $this->files;
    }

}