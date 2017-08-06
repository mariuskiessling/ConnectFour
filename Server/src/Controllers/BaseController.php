<?php

use \ConnectFour\Helpers\Logger as Logger;

class BaseController {
    protected $db;

    public function __construct()
    {
        session_start();

        include(__DIR__.'/../config.php');

        $this->db = @new mysqli($config['DB']['host'], $config['DB']['username'], $config['DB']['password'], $config['DB']['database']);

        if($this->db->connect_error)
        {
            Logger::Log("The connection to a MySQL server failed. Please check your configuration and server status.", Logger::$ERROR);

            die();
        }
    }

    public function redirectOnMissingAuthentication()
    {
        if(!isset($_SESSION['userId']))
        {
            header('Location: /login');
            die();
        }
    }

    public function generateSecure384Hash()
    {
        $randomBytes = openssl_random_pseudo_bytes(384);
        return hash('sha384', $randomBytes);
    }
}
