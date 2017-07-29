<?php

class BaseController {
    protected $db;

    public function __construct()
    {
        session_start();

        include(__DIR__.'/../config.php');

        $this->db = new mysqli($config['DB']['host'], $config['DB']['username'], $config['DB']['password'], $config['DB']['database']);
        // TODO: Add error handling on connection error (HTTP 500)
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
