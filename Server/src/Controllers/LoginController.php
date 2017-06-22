<?php

class LoginController {
    public function __construct() {}

    public function showLogin()
    {
    }

    public function login()
    {
        if(isset($_POST['identifier']) && isset($_POST['password']))
        {
            // Check if user tries to login using an email address or an username
            if(filter_var($_POST['identifier'], FILTER_VALIDATE_EMAIL))
            {
                // Email login
            } else
            {
                // Username login
            }
        } else
        {
            
        }
    }
}
