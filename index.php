<?php

include_once './vendor/autoload.php';

// Load Dolibarr environment
use CodaImporter\Http\Web\Response\Redirect;
use CodaImporter\Http\Web\Response\Response;
use CodaImporter\Infrastructure\Common\Routes\RouteManager;

$res=0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (! $res && ! empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) $res=@include $_SERVER["CONTEXT_DOCUMENT_ROOT"]."/main.inc.php";
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME'])?'':$_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) { $i--; $j--; }
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/main.inc.php")) $res=@include substr($tmp, 0, ($i+1))."/main.inc.php";
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/main.inc.php")) $res=@include dirname(substr($tmp, 0, ($i+1)))."/main.inc.php";
// Try main.inc.php using relative path
if (! $res && file_exists("../main.inc.php")) $res=@include "../main.inc.php";
if (! $res && file_exists("../../main.inc.php")) $res=@include "../../main.inc.php";
if (! $res && file_exists("../../../main.inc.php")) $res=@include "../../../main.inc.php";
if (! $res) die("Include of main fails");

require_once DOL_DOCUMENT_ROOT.'/core/lib/files.lib.php';
require_once DOL_DOCUMENT_ROOT.'/compta/paiement/class/paiement.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/fourn/class/fournisseur.facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/sociales/class/chargesociales.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';


// Load translation files required by the page
$langs->loadLangs(array("codaimporter@codaimporter"));

global $db, $user;

$routes = require './Infrastructure/Common/Routes/routes.conf.php';
$routesGuards = require './Infrastructure/Common/Routes/guards.conf.php';

$response = null;
try{
    $routeName = GETPOST('r');

    $routeManager = new RouteManager($db);
    $routeManager->load($routes);
    $routeManager->loadGuards($routesGuards);

    $response = $routeManager->__invoke($routeName, $user);

    if($response instanceof Redirect){
        if (headers_sent()) {
            echo(sprintf("<script>location.href='%s'</script>", $response->getUrl()));
            exit;
        }

        header(sprintf("Location: %s", $response->getUrl()));
        exit;
    }

}catch (\Exception $e){
    dol_syslog($e->getMessage(), LOG_ERR);
    $response = new Response($e->getMessage());
}

llxHeader('', 'Coda importer', '');


include $response->getTemplate();

// End of page
llxFooter();
$db->close();

