<?php


namespace CodaImporter\Application\Coda\Matcher;


use CodaImporter\Application\Coda\ViewModel\AccountancyFile;
use CodaImporter\Application\Coda\ViewModel\MatchingLevel;
use Codelicious\Coda\Statements\Transaction;

final class GoodMatcher extends BaseMatcher
{

    /**
     * Matches the amount and the third party
     *
     * {@inheritDoc}
     */
    public function matches(Transaction $transaction, AccountancyFile $file)
    {
        if(!$this->equalsAmount($transaction->getAmount(), $file->getAmount())){
            return false;
        }

        if(!$this->equalsName($transaction->getAccount()->getName(), $file->getThirdParty())){
            return false;
        }

        return true;
    }

    /**
     * @return MatchingLevel
     */
    public function matchingLevel()
    {
        return MatchingLevel::SUCCESS();
    }


}