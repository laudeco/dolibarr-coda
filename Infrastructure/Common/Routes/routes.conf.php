<?php

global $db;

use CodaImporter\Http\Web\Controller\ImportController;
use CodaImporter\Http\Web\Controller\IndexController;
use CodaImporter\Infrastructure\Common\Routes\Route;

return [
    new Route('import', ImportController::class, 'get'),
    new Route('importStepOne', ImportController::class, 'stepOne'),
    new Route('importStepTwo', ImportController::class, 'stepTwo'),
    new Route('importStepThree', ImportController::class, 'stepThree'),
    new Route('', ImportController::class, 'get'),
];
?>