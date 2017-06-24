<?php

class BaseController {
    public function __construct()
    {
        session_start();
    }
}
