<?php


namespace CodaImporter\Application\Coda\Matcher;


abstract class BaseMatcher implements MatcherInterface
{
    /**
     * Does the message contain the reference.
     *
     * @param string $message
     * @param string $reference
     *
     * @return bool
     */
    protected function containsReference($message, $reference){
        $cleanMessage = $this->clean($message);
        $cleanReference = $this->clean($reference);

        return strpos($cleanMessage,$cleanReference);
    }

    /**
     * @param string $message
     *
     * @return string
     */
    private function clean($message)
    {
        return preg_replace('/[^a-zA-Z0-9]/i', '', strtolower($message));
    }

    /**
     * Does the message equals to the reference.
     *
     * @param string $message
     * @param string $reference
     *
     * @return bool
     */
    protected function equals($message, $reference)
    {
        $cleanMessage = $this->clean($message);
        $cleanReference = $this->clean($reference);

        return $cleanMessage === $cleanReference;
    }

    protected function equalsName(string $name, string $otherName): bool
    {
        $name = trim($name);
        $otherName = trim($otherName);

        $names = explode(' ', $name);
        $otherNames = explode(' ', $otherName);

        $test = [];

        foreach ($otherNames as $currentName) {
            $test[] = strtolower($currentName);
        }

        $nbrMatch = 0;
        foreach ($names as $currentName) {
            if (in_array(strtolower($currentName), $test)) {
                $nbrMatch++;
            }
        }

        return count($names) === $nbrMatch;
    }

    /**
     * @param float $amount
     * @param float $fileAmount
     *
     * @return bool
     */
    protected function equalsAmount($amount, $fileAmount){
        return abs($amount-$fileAmount) < 0.00001;
    }
}