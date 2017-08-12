<?php

use \ConnectFour\Helpers\Logger as Logger;

class BaseController {
    protected $db;

    public function __construct()
    {
        session_start();

        include(__DIR__.'/../config.php');

        $this->db = @new mysqli($config['DB']['host'], $config['DB']['username'], $config['DB']['password'], $config['DB']['database']);
        $this->db->set_charset('utf8');

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
        } else {
            // Check if user has not changed his user agent and IP address since
            // his last authentication
            if($_SESSION['clientUserAgent'] != $_SERVER['HTTP_USER_AGENT']
                || $_SESSION['clientIPAddress'] != $_SERVER['REMOTE_ADDR'])
            {
                session_destroy();
                header('Location: /login');
                die();
            }
        }
    }

    public function redirectOnMissingAuthenticationREST()
    {
        if(!isset($_SESSION['userId']))
        {
            http_response_code(401);
            echo json_encode([
                'error' => 'Authentication missing. Please start a new session.'
            ]);
            die();
        } else {
            // Check if user has not changed his user agent and IP address since
            // his last authentication
            if($_SESSION['clientUserAgent'] != $_SERVER['HTTP_USER_AGENT']
                || $_SESSION['clientIPAddress'] != $_SERVER['REMOTE_ADDR'])
            {
                session_destroy();
                http_response_code(401);
                echo json_encode([
                    'error' => 'Authentication missing. Please start a new session.'
                ]);
                die();
            }
        }
    }

    public function generateSecure384Hash()
    {
        $randomBytes = openssl_random_pseudo_bytes(384);
        return hash('sha384', $randomBytes);
    }
}
