<?php


namespace CodaImporter\Application\Coda\Matcher;


use CodaImporter\Application\Coda\ViewModel\AccountancyFile;
use CodaImporter\Application\Coda\ViewModel\MatchingLevel;
use Codelicious\Coda\Statements\Transaction;

final class OnlyCommunicationMatcher extends BaseMatcher
{

    /**
     * Matches the communication
     *
     * {@inheritDoc}
     */
    public function matches(Transaction $transaction, AccountancyFile $file)
    {

        if(($transaction->getAmount() < 0 && $file->getAmount() > 0) || ($transaction->getAmount() > 0 && $file->getAmount() < 0)){
            return false;
        }

        if(!$this->containsReference($transaction->getMessage(), $file->getReference())){
            return false;
        }

        return true;
    }

    /**
     * @return MatchingLevel
     */
    public function matchingLevel()
    {
        return MatchingLevel::DANGER();
    }
}