<?php


namespace CodaImporter\Application\Coda\ViewModel;


use CodaImporter\Application\Common\ViewModel\ViewModel;

final class AccountancyFile extends ViewModel
{
    /**
     * @var string
     */
    private $thirdParty;

    /**
     * @var string
     */
    private $thirdPartyBankAccount;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * AccountancyFile constructor.
     * @param string $thirdParty
     * @param string $thirdPartyBankAccount
     * @param float $amount
     * @param string $reference
     * @param int $id
     * @param string $type
     */
    public function __construct($thirdParty, $thirdPartyBankAccount, $amount, $reference, $id, $type)
    {
        $this->thirdParty = $thirdParty;
        $this->thirdPartyBankAccount = $thirdPartyBankAccount;
        $this->amount = $amount;
        $this->reference = $reference;
        $this->id = $id;
        $this->type = $type;
    }


    /**
     * @param array $values
     *
     * @return mixed
     */
    public static function fromArray(array $values)
    {
        return new self(
            $values['thirdpartyName'],
            $values['thirdpartyBank'],
            $values['amount'],
            $values['reference'],
            $values['id'],
            $values['type']
        );
    }

    /**
     * @return string
     */
    public function getThirdParty()
    {
        return $this->thirdParty;
    }

    /**
     * @return string
     */
    public function getThirdPartyBankAccount()
    {
        return $this->thirdPartyBankAccount;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


}