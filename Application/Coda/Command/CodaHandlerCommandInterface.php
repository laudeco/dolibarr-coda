<?php


namespace CodaImporter\Application\Coda\Command;


use Codelicious\Coda\Statements\Transaction;

interface CodaHandlerCommandInterface
{

    public function handle(Transaction $transaction, int $fileId, int $bankAccountId);
}