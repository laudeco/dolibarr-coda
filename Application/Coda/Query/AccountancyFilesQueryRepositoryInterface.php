<?php


namespace CodaImporter\Application\Coda\Query;


use CodaImporter\Application\Coda\ViewModel\AccountancyFile;

interface AccountancyFilesQueryRepositoryInterface
{

    /**
     * @param array $criteria
     *
     * @return AccountancyFile[]
     */
    public function query(array $criteria);
}