<?php


namespace CodaImporter\Application\Coda\Command;


use CodaImporter\Application\Coda\ViewModel\AccountancyFileType;

final class CodaHandlerFactory
{

    public static function instance(\DoliDB $db, string $fileType): CodaHandlerCommandInterface
    {
        global $user;

        switch($fileType){
            case AccountancyFileType::BILL:
                return new CustomerInvoiceCodaHandler($db, $user);
            case AccountancyFileType::BILL_SUPPLIER:
                return new SupplierInvoiceCodaHandler($db, $user);
            case AccountancyFileType::EXPENSE_REPORT:
                return new ExpenseReportCodaHandler($db, $user);
            default:
                return new EmptyCodaHandler($db, $user);
        }
    }
}