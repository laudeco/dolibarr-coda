<?php


namespace CodaImporter\Infrastructure\Coda\Repository\Query;


use CodaImporter\Application\Coda\Query\AccountancyFilesQueryRepositoryInterface;
use CodaImporter\Application\Coda\ViewModel\AccountancyFile;

final class AccountancyFilesQueryRepository implements AccountancyFilesQueryRepositoryInterface
{
    /**
     * @var \DoliDB
     */
    private $db;

    /**
     * @param \DoliDB $db
     */
    public function __construct(\DoliDB $db)
    {
        $this->db = $db;
    }

    /**
     * {@inheritDoc}
     */
    public function query(array $criteria)
    {
        $sql = "
        -- Customer
SELECT
  llx_facture.rowid as id,
  llx_facture.ref as reference,
  llx_facture.total_ttc as amount,
  'llx_facture' as type,
  llx_societe.nom as thirdpartyName,
  llx_societe_rib.iban_prefix
  

FROM llx_facture

INNER JOIN llx_societe
  ON llx_societe.rowid = llx_facture.fk_soc
LEFT JOIN llx_societe_rib
  ON (llx_societe_rib.fk_soc = llx_societe.rowid AND llx_societe_rib.default_rib = 1)
    
WHERE
  llx_facture.fk_statut = 1
  AND llx_facture.paye = 0

UNION

-- Founrisseurs
SELECT
  llx_facture_fourn.rowid as id,
  llx_facture_fourn.ref_supplier as reference,
  llx_facture_fourn.total_ttc * -1 as amount,
  'llx_facture_fourn' as type,
  llx_societe.nom as thirdpartyName,
  llx_societe_rib.iban_prefix

FROM llx_facture_fourn

INNER JOIN llx_societe
  ON llx_societe.rowid = llx_facture_fourn.fk_soc
LEFT JOIN llx_societe_rib
  ON (llx_societe_rib.fk_soc = llx_societe.rowid AND llx_societe_rib.default_rib = 1)
    
WHERE
  llx_facture_fourn.fk_statut = 1
  AND llx_facture_fourn.paye = 0

UNION 

-- Expense report
SELECT
  llx_expensereport.rowid as id,
  llx_expensereport.ref as reference,
  llx_expensereport.total_ttc * -1 as amount,
  'llx_expensereport' as type,
  CONCAT(llx_user.lastname, ' ', llx_user.firstname) as thirdpartyName,
  llx_user_rib.iban_prefix

FROM llx_expensereport

INNER JOIN llx_user
  ON llx_user.rowid = llx_expensereport.fk_user_author
LEFT JOIN llx_user_rib
  ON (llx_user_rib.fk_user = llx_user.rowid)
    
WHERE
  llx_expensereport.fk_statut = 5
  AND llx_expensereport.paid = 0";

        $resql = $this->db->query($sql);
        if (!$resql) {
            throw new \Exception('Bill not found');
        }

        $num = $this->db->num_rows($resql);
        if ($num == 0) {
            return [];
        }

        $documents = [];
        for($i = 0; $i < $num ; $i++) {
            $properties = $this->db->fetch_array($resql);
            $documents[] = AccountancyFile::fromArray($properties);
        }

        return $documents;
    }
}