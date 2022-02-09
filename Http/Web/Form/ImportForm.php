<?php


namespace CodaImporter\Http\Web\Form;

use CodaImporter\Infrastructure\Common\Form\Csrf;
use CodaImporter\Infrastructure\Common\Form\Form;
use CodaImporter\Infrastructure\Common\Form\FormInterface;
use CodaImporter\Infrastructure\Common\Form\Hidden;
use CodaImporter\Infrastructure\Common\Form\InputFile;

final class ImportForm extends Form implements FormInterface
{

    /**
     * @param string $name
     * @param \DoliDB $db
     */
    public function __construct($name, \DoliDB $db)
    {
        parent::__construct($name, FormInterface::METHOD_POST);
        $this->add(new InputFile('coda'));
        $this->add(new Csrf('token'));
    }
}