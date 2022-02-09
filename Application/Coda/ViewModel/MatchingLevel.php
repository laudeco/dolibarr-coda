<?php


namespace CodaImporter\Application\Coda\ViewModel;


final class MatchingLevel
{

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     */
    private function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return MatchingLevel
     */
    public static function BINGO(){
        return new self(0);
    }

    /**
     * @return MatchingLevel
     */
    public static function SUCCESS(){
        return new self(1);
    }

    /**
     * @return MatchingLevel
     */
    public static function WARNING(){
        return new self(2);
    }

    /**
     * @return MatchingLevel
     */
    public static function DANGER(){
        return new self(3);
    }

    /**
     * @return int
     */
    public function level()
    {
        return $this->value;
    }



}