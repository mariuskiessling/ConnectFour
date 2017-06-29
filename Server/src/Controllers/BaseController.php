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
}
