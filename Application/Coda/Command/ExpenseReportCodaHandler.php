<?php


namespace CodaImporter\Application\Coda\Command;


use Codelicious\Coda\Statements\Transaction;
use PaymentExpenseReport;

final class ExpenseReportCodaHandler extends AbstractCodaHandler
{
    protected function handlePayment(Transaction $transaction, int $fileId)
    {
        $payment = new PaymentExpenseReport($this->db);
        $payment->fk_expensereport = $fileId;
        $payment->datepaid       = $transaction->getTransactionDate()->getTimestamp();
        $payment->amounts        = [
            $transaction->getTransactionSequence() => $transaction->getAmount()
        ];
        $payment->fk_typepayment = 2; //TODO move to the config
        $payment->note_public    = $transaction->getMessage() . ' '.$transaction->getStructuredMessage();

        $paymentid = $payment->create($this->user);

        if ($paymentid < 0)
        {
            throw new \Exception($payment->error);
        }

        return $payment;
    }

    protected function handleBankPayment(Transaction $transaction, int $fileId, \CommonObject $payment, int $bankAccountId)
    {
        if(!($payment instanceof PaymentExpenseReport)){
            return;
        }

        $result = $payment->addPaymentToBank($this->user, 'payment_expensereport', '(ExpenseReportPayment)', $bankAccountId, '', '');
        if ($result < 0)
        {
            throw new \Exception($payment->error);
        }


    }

    protected function markAsPaid(Transaction $transaction, int $fileId)
    {
        $expensereport = $this->getExpensereport($fileId);

        if ($expensereport->total_ttc - $transaction->getAmount() > 0) {
            return;
        }

        $result = $expensereport->set_paid($expensereport->id, $this->user);

        if ($result < 0) {
            throw new \Exception('While setting expense report as paid');
        }
    }


    private function getExpensereport(int $id){
        $expenseReport = new \ExpenseReport($this->db);
        $expenseReport->fetch($id);

        return $expenseReport;
    }
}