<?php

use \ConnectFour\Helpers\Logger as Logger;

class Template {
    public static function Render($filename, $args = [])
    {
        $templateFilename = __DIR__.'/../templates/'.$filename.'.tpl.php';
        if(file_exists($templateFilename))
        {
            extract($args);
            include($templateFilename);
        } else
        {
            Logger::Log("Template ".$filename." not found in file ".$templateFilename.".", Logger::$WARNING);
        }
    }
}
