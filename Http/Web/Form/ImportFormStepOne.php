<?php


namespace CodaImporter\Http\Web\Form;


use CodaImporter\Infrastructure\Common\Form\Csrf;
use CodaImporter\Infrastructure\Common\Form\Form;
use CodaImporter\Infrastructure\Common\Form\FormInterface;
use CodaImporter\Infrastructure\Common\Form\Hidden;

final class ImportFormStepOne extends Form implements FormInterface
{
    /**
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name, FormInterface::METHOD_POST);
        $this->add(new Hidden('coda'));
        $this->add(new Csrf('token'));
    }
}