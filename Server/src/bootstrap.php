<?php

require_once(__DIR__.'/config.php');

require_once(__DIR__.'/helpers/Logger.php');
require_once(__DIR__.'/helpers/Router.php');
require_once(__DIR__.'/helpers/Template.php');

// Enable all error messages in an development environment
if($config['environment'] == 'development')
{
    error_reporting(E_ALL);
} elseif($config['environment'] == 'production')
{
    error_reporting(0);
}

require_once(__DIR__.'/routes.php');
