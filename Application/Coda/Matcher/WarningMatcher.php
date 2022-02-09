<?php


namespace CodaImporter\Application\Coda\Matcher;


use CodaImporter\Application\Coda\ViewModel\AccountancyFile;
use CodaImporter\Application\Coda\ViewModel\MatchingLevel;
use Codelicious\Coda\Statements\Transaction;

final class WarningMatcher extends BaseMatcher
{

    /**
     * Matches the amount and the communication
     *
     * {@inheritDoc}
     */
    public function matches(Transaction $transaction, AccountancyFile $file)
    {
        if(!$this->equalsAmount($transaction->getAmount(), $file->getAmount())){
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
        return MatchingLevel::WARNING();
    }
}