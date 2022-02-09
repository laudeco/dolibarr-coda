<?php


namespace CodaImporter\Application\Coda\Command;


use Codelicious\Coda\Statements\Transaction;

abstract class AbstractCodaHandler implements CodaHandlerCommandInterface
{


    /**
     * @var \DoliDB
     */
    protected $db;

    /**
     * @var \User
     */
    protected $user;

    public function __construct(\DoliDB $db, \User $user)
    {
        $this->db = $db;
        $this->user = $user;
    }

    public function handle(Transaction $transaction, int $fileId, int $bankAccountId)
    {
        $this->db->begin();

        $payment = $this->handlePayment($transaction, $fileId);
        if(null === $payment){
            $this->db->rollback();
            return;
        }

        $this->handleBankPayment($transaction, $fileId, $payment, $bankAccountId);

        $this->markAsPaid($transaction, $fileId);

        $this->handleThirdPartyBankAccount($transaction, $fileId);

        $this->db->commit();
    }

    protected function handleThirdPartyBankAccount(Transaction $transaction, int $fileId)
    {
    }

    /**
     * Attaches the payment to the bank account
     * @param Transaction $transaction
     * @param int $fileId
     * @param \CommonObject $payment
     * @param int $bankAccountId
     */
    protected function handleBankPayment(Transaction $transaction, int $fileId, \CommonObject $payment, int $bankAccountId)
    {

    }

    /**
     * Handles the payment on the piece.
     *
     * @param Transaction $transaction
     * @param int $fileId
     *
     * @return null|\CommonObject
     */
    protected function handlePayment(Transaction $transaction, int $fileId)
    {
        return null;
    }

    protected function markAsPaid(Transaction $transaction, int $fileId)
    {
    }

}