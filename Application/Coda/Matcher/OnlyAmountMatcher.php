<?php


namespace CodaImporter\Application\Coda\Matcher;


use CodaImporter\Application\Coda\ViewModel\AccountancyFile;
use CodaImporter\Application\Coda\ViewModel\MatchingLevel;
use Codelicious\Coda\Statements\Transaction;

final class OnlyAmountMatcher extends BaseMatcher
{

    /**
     * Matches the amount
     *
     * {@inheritDoc}
     */
    public function matches(Transaction $transaction, AccountancyFile $file)
    {
        if(!$this->equalsAmount($transaction->getAmount(), $file->getAmount())){
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