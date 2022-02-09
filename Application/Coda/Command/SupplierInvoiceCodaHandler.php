<?php


namespace CodaImporter\Application\Coda\Command;


use Codelicious\Coda\Statements\Transaction;
use FactureFournisseur;
use PaiementFourn;
use Societe;

final class SupplierInvoiceCodaHandler extends AbstractCodaHandler
{
    protected function handlePayment(Transaction $transaction, int $fileId)
    {
        // Creation de la ligne paiement
        $paiement = new PaiementFourn($this->db);
        $paiement->datepaye     = $transaction->getValutaDate()->getTimestamp();
        $paiement->amounts      = [$fileId => $transaction->getAmount()]; // Array of amounts
        $paiement->multicurrency_amounts = [];
        $paiement->paiementid   = GETPOST('paiementid', 'int');

//        $paiement->num_payment  = GETPOST('num_paiement', 'alphanohtml');
//        $paiement->num_paiement = $paiement->num_payment; // For backward compatibility

        $paiement->note_private = sprintf('(%s/%s) %s - %s',$transaction->getTransactionSequence(), $transaction->getStatementSequence(), $transaction->getStructuredMessage(),$transaction->getMessage());
        $paiement->note         = $paiement->note_private; // For backward compatibility

        $paiement_id = $paiement->create($this->user, 1, $this->getInvoiceThirdparty($fileId));
        if ($paiement_id < 0)
        {
            throw new \Exception();
        }

    }

    /**
     * @param Transaction $transaction
     * @param int $fileId
     * @param \CommonObject|PaiementFourn $payment
     * @param int $bankAccountId
     * @throws \Exception
     */
    protected function handleBankPayment(
        Transaction $transaction,
        int $fileId,
        \CommonObject $payment,
        int $bankAccountId
    ) {
        if(!$this->supportedPaymentClass($payment)){
            throw new \Exception('Unsupported payment class');
        }


        $payment->addPaymentToBank($this->user, 'payment_supplier', '(SupplierInvoicePayment)', $bankAccountId, '', '');
    }


    private function getInvoiceThirdparty(int $invoiceId){
        $tmpinvoice = new FactureFournisseur($this->db);
        $tmpinvoice->fetch($invoiceId);

        $supplier = $tmpinvoice->fetch_thirdparty();
        if($supplier === -1){
            throw new \Exception('Customer not found');
        }

        return $tmpinvoice->thirdparty;
    }

    private function supportedPaymentClass(\CommonObject $payment): bool
    {
        return ($payment instanceof PaiementFourn);
    }
}