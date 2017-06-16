<?php

namespace ConnectFour\Helpers;

class Logger {
    public static $ERROR = 1;
    public static $WARNING = 2;
    public static $INFO = 3;
    public static $DEBUG = 4;

    public static function Log($message, $level) {
        include(__DIR__.'/../config.php');

        if($config['environment'] == "development")
        {
            switch($level)
            {
                case self::$ERROR:
                    echo('<b style="font-size: 15px; font-family: Arial;">Error: </b><pre style="font-size: 15px; font-family: Arial; display: inline;">');
                    break;
                case self::$WARNING:
                    echo('<b style="font-size: 15px; font-family: Arial;">Warning: </b><pre style="font-size: 15px; font-family: Arial; display: inline;">');
                    break;
                case self::$INFO:
                    echo('<b style="font-size: 15px; font-family: Arial;">Info: </b><pre style="font-size: 15px; font-family: Arial; display: inline;">');
                    break;
                case self::$DEBUG:
                    echo('<b style="font-size: 15px; font-family: Arial;">DEBUG: </b><pre style="font-size: 15px; font-family: Arial; display: inline;">');
                    break;
            }

            print_r($message);
            echo('</pre>');

        }
    }
}
