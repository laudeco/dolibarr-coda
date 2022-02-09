<?php


namespace CodaImporter\Application\Coda\Matcher;


use CodaImporter\Application\Coda\ViewModel\AccountancyFile;
use CodaImporter\Application\Coda\ViewModel\MatchingLevel;
use Codelicious\Coda\Statements\Transaction;

interface MatcherInterface
{

    /**
     * @param Transaction $transaction
     *
     * @param AccountancyFile $file
     *
     * @return bool
     */
    public function matches(Transaction $transaction, AccountancyFile $file);

    /**
     * @return MatchingLevel
     */
    public function matchingLevel();
}