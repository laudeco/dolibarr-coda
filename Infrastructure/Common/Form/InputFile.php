<?php


namespace CodaImporter\Infrastructure\Common\Form;


final class InputFile extends BaseInput
{
    /**
     * @param string $name
     * @param array  $options
     */
    public function __construct($name, array $options = [])
    {
        parent::__construct($name, FormElementInterface::TYPE_FILE, $options);
    }
}