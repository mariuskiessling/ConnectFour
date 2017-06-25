<?php

class LoginController extends BaseController {
    private $db;

    public function __construct()
    {
        parent::__construct();
        include(__DIR__.'/../config.php');

        $this->db = new mysqli($config['DB']['host'], $config['DB']['username'], $config['DB']['password'], $config['DB']['database']);
        // TODO: Add error handling on connection error (HTTP 500)
    }

    public function showLogin()
    {
        Template::Render('login', [
            'title' => "Login",
        ]);
    }

    public function login()
    {
        if(isset($_POST['identifier']) && isset($_POST['password']))
        {
            // Check if user tries to login using an email address or an username
            if(filter_var($_POST['identifier'], FILTER_VALIDATE_EMAIL))
            {
                // Email login
                $sql = 'SELECT id, password FROM users WHERE email = ? LIMIT 1';
            } else
            {
                // Username login
                $sql = 'SELECT id, password FROM users WHERE username = ? LIMIT 1';
            }

            $query = $this->db->prepare($sql);
            $query->bind_param('s', $_POST['identifier']);
            $query->execute();
            $result = $query->get_result();

            if($result->num_rows != 0)
            {
                $resultData = $result->fetch_array();
                if(password_verify($_POST['password'], $resultData['password']))
                {
                    $_SESSION['userId'] = $resultData['id'];
                    header('Location: /lobby');
                    die();
                } else {
                    header('Location: /login?error');
                    die();
                }
            } else
            {
                header('Location: /login?error');
                die();
            }
        } else
        {
            header('Location: /login?error');
            die();
        }
    }
}