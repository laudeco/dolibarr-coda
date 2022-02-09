<?php


namespace CodaImporter\Application\Coda\ViewModel;


final class MatchingAccountancyFile
{

    /**
     * @var MatchingLevel
     */
    private $matchingLevel;

    /**
     * @var AccountancyFile
     */
    private $file;

    /**
     * @param AccountancyFile $file
     * @param MatchingLevel $matchingLevel
     */
    public function __construct(AccountancyFile $file, MatchingLevel $matchingLevel)
    {
        $this->matchingLevel = $matchingLevel;
        $this->file = $file;
    }

    /**
     * @return AccountancyFile
     */
    public function file()
    {
        return $this->file;
    }

    /**
     * @return MatchingLevel
     */
    public function level()
    {
        return $this->matchingLevel;
    }


}