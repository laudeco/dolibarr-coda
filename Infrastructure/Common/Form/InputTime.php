<?php

namespace CodaImporter\Infrastructure\Common\Form;


class InputTime extends BaseInput
{
    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct($name, array $options = [])
    {
        parent::__construct($name, FormElementInterface::TYPE_TIME, $options);
    }
}