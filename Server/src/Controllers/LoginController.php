<?php

class LoginController extends BaseController {

    public function __construct()
    {
        parent::__construct();
    }

    public function showLogin()
    {
        if(!isset($_SESSION['userId']))
        {
            Template::Render('login', [
                'title' => "Login",
            ]);
        } else
        {
            header('Location: /lobby');
            die();
        }
    }

    public function login()
    {
        if(isset($_POST['identifier']) && isset($_POST['password']))
        {
            // Check if user tries to login using an email address or an username
            if(filter_var($_POST['identifier'], FILTER_VALIDATE_EMAIL))
            {
                // Email login
                $sql = 'SELECT id, password, activated FROM users WHERE email = ? LIMIT 1';
            } else
            {
                // Username login
                $sql = 'SELECT id, password, activated FROM users WHERE username = ? LIMIT 1';
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
                    if($resultData['activated'] == 1)
                    {
                        $_SESSION['userId'] = $resultData['id'];
                        header('Location: /lobby');
                        die();
                    } else
                    {
                        Template::Render('login', [
                            'title' => "Login",
                            'notification' => [
                                'title' => 'Fehler',
                                'message' => 'Bei der Anmeldung ist ein Fehler aufgetreten. Sie haben Ihren Account noch nicht aktiviert.',
                                'icon' => 'icon_error-circle_alt'
                            ],
                            'identifier' => $_POST['identifier']
                        ]);
                        die();
                    }
                } else {
                    Template::Render('login', [
                        'title' => "Login",
                        'notification' => [
                            'title' => 'Fehler',
                            'message' => 'Bei der Anmeldung ist ein Fehler aufgetreten. Bitte prüfen Sie Ihre Anmeldedaten.',
                            'icon' => 'icon_error-circle_alt'
                        ],
                        'identifier' => $_POST['identifier']
                    ]);
                    die();
                }
            } else
            {
                Template::Render('login', [
                    'title' => "Login",
                    'notification' => [
                        'title' => 'Fehler',
                        'message' => 'Bei der Anmeldung ist ein Fehler aufgetreten. Bitte prüfen Sie Ihre Anmeldedaten.',
                        'icon' => 'icon_error-circle_alt'
                    ],
                    'identifier' => $_POST['identifier']
                ]);
                die();
            }
        } else
        {
            Template::Render('login', [
                'title' => "Login",
                'notification' => [
                    'title' => 'Fehler',
                    'message' => 'Bei der Anmeldung ist ein Fehler aufgetreten. Bitte prüfen Sie Ihre Anmeldedaten.',
                    'icon' => 'icon_error-circle_alt'
                ],
                'identifier' => $_POST['identifier']
            ]);
            die();
        }
    }

    public function logout()
    {
        $this->redirectOnMissingAuthentication();
        
        session_destroy();
        header('Location: /login');
        die();
    }
}
