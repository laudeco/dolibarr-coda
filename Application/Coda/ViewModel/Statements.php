<?php


namespace CodaImporter\Application\Coda\ViewModel;


use Codelicious\Coda\Statements\Statement;
use Codelicious\Coda\Statements\Transaction;

final class Statements
{
    /**
     * @var Statement[]
     */
    private $statements;

    /**
     * @param Statement[] $statements
     */
    public function __construct(array $statements = [])
    {
        $this->statements = $statements;
    }

    public static function fromStatements(array $statements): self
    {
        foreach($statements as $test){
            if(!($test instanceof Statement)){
                throw new \InvalidArgumentException('Should be an instance of'.Statement::class);
            }
        }

        return new self($statements);
    }

    /**
     * @return array|Transaction[]
     */
    public function transactions(): array{
        $transactions = [];

        foreach($this->statements as $statement){
            $transactions = array_merge($transactions, $statement->getTransactions());
        }

        return $transactions;
    }

    public function findTransaction(int $statementSequence, int $transactionSequence)
    {
        foreach($this->transactions() as $transaction){
            if($transaction->getStatementSequence() !== $statementSequence || $transaction->getTransactionSequence() !== $transactionSequence){
                continue;
            }

            return $transaction;
        }

        return null;
    }

}