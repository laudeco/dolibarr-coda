<?php


namespace CodaImporter\Application\Coda\ViewModel;


use Codelicious\Coda\Statements\Transaction;

final class MatchResult
{

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var AccountancyFile[]
     */
    private $files;

    /**
     * @param Transaction $transaction
     * @param array|AccountancyFile[] $accountingFiles
     */
    public function __construct(Transaction $transaction, array $accountingFiles = [])
    {
        $this->transaction = $transaction;
        $this->files = $accountingFiles;
    }

    public function addMatch(MatchingAccountancyFile $matchingFile)
    {

        $files = $this->files;
        $level = $matchingFile->level()->level();

        if (!isset($files[$level])) {
            $files[$level] = [];
        }

        $files[$level][] = $matchingFile;

        return new self($this->transaction, $files);
    }

    public function transaction()
    {
        return $this->transaction;
    }

    /**
     * @return array|MatchingAccountancyFile[]
     */
    public function files()
    {
        $result = [];

        foreach($this->files as $level => $files){
            foreach($files as $currentFile){
                $result[] = $currentFile;
            }
        }

        return $result;
    }

    public function hasMatch(){
        return !empty($this->files);
    }


}