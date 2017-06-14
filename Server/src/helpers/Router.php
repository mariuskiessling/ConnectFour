<?php

namespace ConnectFour\Helpers;

class Router {
    public static function Request($method, $path, $action, $params = [])
    {
        if($_SERVER['REQUEST_METHOD'] == $method) {
            if($_SERVER['PATH_INFO'] == $path)
            {
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
        } else
        {
            // TODO: Implement method not found for route.
        }
    }

    // TODO: Escape controller filenames before accessing filesystem.
    private static function CallController($controllerFilename, $function = "", $params = []) {
        $controllerFilepath = __DIR__.'/../Controllers/'.$controllerFilename.'.php';

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
                        // TODO: Implement function not found in controller logging.
                    }
                }

                return $controller;
            } else {
                // TODO: Implement controller class not found logging.
            }
        } else {
            // TODO: Implement controller not found logging.
        }
    }
}
