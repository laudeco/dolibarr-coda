<?php


namespace CodaImporter\Application\Coda\Command;


use Codelicious\Coda\Statements\Transaction;
use Facture;
use Paiement;
use PaymentExpenseReport;

final class CustomerInvoiceCodaHandler extends AbstractCodaHandler
{
    protected function handlePayment(Transaction $transaction, int $fileId)
    {
        $paiement = new Paiement($this->db);

        $paiement->datepaye     = $transaction->getValutaDate()->getTimestamp();
        $paiement->amounts      = [$fileId => $transaction->getAmount()]; // Array with all payments dispatching with invoice id
        $paiement->multicurrency_amounts = []; // Array with all payments dispatching
        $paiement->paiementid   = dol_getIdFromCode($this->db, 'VIR', 'c_paiement', 'code', 'id', 1);

//        $paiement->num_payment  = GETPOST('num_paiement', 'alpha');
//        $paiement->num_paiement = $paiement->num_payment; // For bacward compatibility

        $paiement->note_private = sprintf('(%s/%s) %s - %s',$transaction->getTransactionSequence(), $transaction->getStatementSequence(), $transaction->getStructuredMessage(),$transaction->getMessage());
        $paiement->note         = $paiement->note_private; // For bacward compatibility

        $id = $paiement->create($this->user, 1, $this->getInvoiceThirdparty($fileId));
        if($id <= 0){
            throw new \Exception('Customer paiement error');

        }

        return $paiement;
    }

    protected function handleBankPayment(Transaction $transaction, int $fileId, \CommonObject $payment, int $bankAccountId) {
        if(!($payment instanceof Paiement)){
            return;
        }


        $label = '(CustomerInvoicePayment)';

        if (GETPOST('type') == Facture::TYPE_CREDIT_NOTE){
            $label = '(CustomerInvoicePaymentBack)'; // Refund of a credit note
        }

        $result = $payment->addPaymentToBank($this->user, 'payment', $label, $bankAccountId, $transaction->getAccount()->getName(), $transaction->getAccount()->getNumber());

        if ($result < 0)
        {
            throw new \Exception('While saving to bank account');
        }

    }


    private function getInvoiceThirdparty(int $invoiceId){
        $tmpinvoice = $this->getInvoice($invoiceId);

        $customer = $tmpinvoice->fetch_thirdparty();
        if($customer === -1){
            throw new \Exception('Customer not found');
        }

        return $tmpinvoice->thirdparty;
    }

    /**
     * @param int $invoiceId
     * @return Facture
     */
    private function getInvoice(int $invoiceId): Facture
    {
        $tmpinvoice = new Facture($this->db);
        $tmpinvoice->fetch($invoiceId);

        return $tmpinvoice;
    }


}