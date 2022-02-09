<?php


namespace CodaImporter\Application\Coda\Query;


use CodaImporter\Application\Coda\Matcher\BingoMatcher;
use CodaImporter\Application\Coda\Matcher\GoodMatcher;
use CodaImporter\Application\Coda\Matcher\MatcherInterface;
use CodaImporter\Application\Coda\Matcher\OnlyAmountMatcher;
use CodaImporter\Application\Coda\Matcher\OnlyCommunicationMatcher;
use CodaImporter\Application\Coda\Matcher\OnlyThirdPartyMatcher;
use CodaImporter\Application\Coda\Matcher\SuccessMatcher;
use CodaImporter\Application\Coda\Matcher\WarningMatcher;
use CodaImporter\Application\Coda\ViewModel\AccountancyFile;
use CodaImporter\Application\Coda\ViewModel\MatchingAccountancyFile;
use CodaImporter\Application\Coda\ViewModel\MatchingLevel;
use CodaImporter\Application\Coda\ViewModel\MatchResult;
use Codelicious\Coda\Statements\Transaction;

final class MatcherQueryHandler
{

    /**
     * @var MatcherInterface[]
     */
    private $matchers;

    /**
     * MatcherQueryHandler constructor.
     */
    public function __construct()
    {

        $this->matchers = [];
        $this->matchers[] = new BingoMatcher(); // Amount + Message + Third party + bank account
        $this->matchers[] = new SuccessMatcher(); // amount + third party + message
        $this->matchers[] = new GoodMatcher(); //Amount + Third party
        $this->matchers[] = new WarningMatcher(); //Amount + Message
        $this->matchers[] = new OnlyAmountMatcher();
        $this->matchers[] = new OnlyCommunicationMatcher();
        $this->matchers[] = new OnlyThirdPartyMatcher();
    }

    public function __invoke(MatcherQuery $query)
    {
        return $this->query($query);
    }

    /**
     * @param MatcherQuery $query
     *
     * @return MatchResult
     */
    public function query(MatcherQuery $query)
    {
        $result = new MatchResult($query->transaction());

        foreach ($query->files() as $file) {
            foreach ($this->matchers as $matcher) {
                if (!$matcher->matches($query->transaction(), $file)) {
                    continue;
                }

                $result = $result->addMatch(new MatchingAccountancyFile($file, $matcher->matchingLevel()));
            }
        }

        return $result;
    }
}