<?php


namespace CodaImporter\Application\Coda\Command;


use Codelicious\Coda\Statements\Transaction;

final class EmptyCodaHandler extends AbstractCodaHandler
{

    public function handle(Transaction $transaction, int $fileId, int $bankAccountId)
    {
        return;
    }
}