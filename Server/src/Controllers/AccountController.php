<?php

class AccountController extends BaseController {
    private $db;

    public function __construct()
    {
        parent::__construct();
        include(__DIR__.'/../config.php');

        $this->db = new mysqli($config['DB']['host'], $config['DB']['username'], $config['DB']['password'], $config['DB']['database']);
        // TODO: Add error handling on connection error (HTTP 500)
    }

    public function activateAccount()
    {
        if(isset($_GET['token']))
        {
            $sql = 'UPDATE users SET activated = 1 WHERE registration_token = ?';
            $query = $this->db->prepare($sql);
            $query->bind_param('s', $_GET['token']);
            $query->execute();

            if($query->affected_rows == 0)
            {
                Template::Render('login', [
                    'title' => "Login",
                    'notification' => [
                        'title' => 'Fehler',
                        'message' => 'Der angebene Token ist ungültig. Es konnte kein Nutzer aktiviert werden.',
                        'icon' => 'icon_error-circle_alt'
                    ]
                ]);
            } else {
                Template::Render('login', [
                    'title' => "Login",
                    'notification' => [
                        'title' => 'Erfolg',
                        'message' => 'Ihr Account wurde erfolgreich aktiviert. Sie können sich nun mit Ihren Daten anmelden.',
                        'icon' => 'icon_check_alt2'
                    ]
                ]);
            }
        } else
        {
            // TODO: Add error handling on missing token
        }
    }
}
