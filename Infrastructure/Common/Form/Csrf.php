<?php


namespace CodaImporter\Infrastructure\Common\Form;


final class Csrf extends Hidden
{

    public function __construct($name = 'token', array $options = [])
    {
        parent::__construct($name, $options);
        $this->setValue(newToken());
    }


}