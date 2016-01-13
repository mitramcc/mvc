<?php

class Autoloader
{
    public static function loadlibs($class)
    {
        $filename = $class . '.php';
        $file ='../app/core/' . $filename;

        if (!file_exists($file))
        {
            return false;
        }
        require_once($file);
        require_once("../app/configs/configs.php");
    }
}

?>