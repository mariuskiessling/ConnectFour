<?php

namespace ConnectFour\Helpers;

use \ConnectFour\Helpers\Logger as Logger;

class Router {
    public static function Request($method, $path, $action, $params = [])
    {
        if(@$_SERVER['PATH_INFO'] == $path)
        {
            if($_SERVER['REQUEST_METHOD'] == $method) {
                // Run action that are structured like Controller@function
                if(preg_match('/((?:[a-zA-Z]+))(@)((?:[a-zA-Z]+))/s', $action))
                {
                    $explodedAction = explode("@", $action);
                    if(isset($explodedAction[0]) && isset($explodedAction[1]))
                    {
                        return self::CallController($explodedAction[0], $explodedAction[1], $params);
                    }
                } elseif(preg_match('/([a-zA-Z]+)+[^@]/s', $action))
                {
                    return self::CallController($action, "", $params);
                }
            }
        }
    }

    // TODO: Escape controller filenames before accessing filesystem.
    private static function CallController($controllerFilename, $function = "", $params = []) {
        $controllerFilepath = __DIR__.'/../Controllers/'.$controllerFilename.'.php';
        require(__DIR__.'/../Controllers/BaseController.php');

        if(file_exists($controllerFilepath))
        {
            require($controllerFilepath);
            $controllerClassName = addslashes($controllerFilename);

            if(class_exists($controllerClassName))
            {
                $controller = new $controllerClassName;

                if($function != "")
                {
                    if(method_exists($controller, $function))
                    {
                        if(!empty($params))
                        {
                            call_user_func_array([$controller, $function], $params);
                        } else
                        {
                            $controller->$function();
                        }
                    } else {
                        Logger::Log("Function ".$function." not found in controller ".$controllerClassName.".", Logger::$WARNING);
                    }
                }

                return $controller;
            } else {
                Logger::Log("Controller class ".$controllerClassName." not found in file ".$controllerFilename.".", Logger::$WARNING);
            }
        } else {
             Logger::Log("Controller file ".$controllerFilename.".php not found.", Logger::$WARNING);
        }
    }
}
