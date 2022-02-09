<?php


namespace CodaImporter\Http\Web\Controller;


use CodaImporter\Http\Web\Response\Response;

final class IndexController extends WebController
{

    public function defaultAction(){
        return $this->render('index/default.phtml');
    }
}