<?php

namespace ConnectFour\Helpers;

class Router {
    
    // TODO: Unsafe implementation!
    public static function Request($path, $action)
    {
        if($_SERVER['PATH_INFO'] == $path)
        {
            eval($action.';');
        }
    }
}
