<?php


namespace CodaImporter\Http\Web\Requests;


final class Request
{

    /**
     * @param string $name
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function getParam($name, $defaultValue = null){
        if($_POST[$name]){
            return $_POST[$name];
        }

        if($_GET[$name]){
            return $_GET[$name];
        }

        return $defaultValue;
    }

    /**
     * @return bool
     */
    public function isPost(){
        return strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter($name){
        return $this->getParam($name) !== null;
    }

    /**
     * @return array
     */
    public function getPostParameters(){
        return $_POST;
    }
}