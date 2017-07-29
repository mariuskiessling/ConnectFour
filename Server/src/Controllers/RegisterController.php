<?php

class RegisterController extends BaseController {

    public function __construct()
    {
        parent::__construct();
    }

    public function showRegistration()
    {
        if(!isset($_SESSION['userId']))
        {
            Template::Render('registration', [
                'title' => "Registrierung",
            ]);
        } else
        {
            header('Location: /lobby');
            die();
        }
    }

    public function register()
    {
        if(isset($_POST['email'])
            && isset($_POST['username'])
            && isset($_POST['password'])
            && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        {
            // Check if email already exists in DB
            $sql = 'SELECT id FROM users WHERE email = ? LIMIT 1';
            $query = $this->db->prepare($sql);
            $query->bind_param('s', $_POST['email']);
            $query->execute();
            $result = $query->get_result();

            if($result->num_rows != 0)
            {
                Template::Render('registration', [
                    'title' => "Registrierung",
                    'notification' => [
                        'title' => 'Fehler',
                        'message' => 'Die angegebene E-Mailadresse ist bereits registriert. Bitte <a href="/login" class="formatted">melden Sie sich an</a>, wenn Sie Inhaber dieser E-Mailadresse sind.',
                        'icon' => 'icon_error-circle_alt'
                    ],
                    'email' => $_POST['email'],
                    'username' => $_POST['username'],
                    'password' => $_POST['password']
                ]);
            } else
            {
                // Check if username is already is use
                $sql = 'SELECT id FROM users WHERE username = ? LIMIT 1';
                $query = $this->db->prepare($sql);
                $query->bind_param('s', $_POST['username']);
                $query->execute();
                $result = $query->get_result();

                if($result->num_rows != 0)
                {
                    Template::Render('registration', [
                        'title' => "Registrierung",
                        'notification' => [
                            'title' => 'Fehler',
                            'message' => 'Der angegebene Nutzername ist bereits registriert. Bitte <a href="/login" class="formatted">melden Sie sich an</a>, wenn Sie Inhaber dieses Nutzernamens sind.',
                            'icon' => 'icon_error-circle_alt'
                        ],
                        'email' => $_POST['email'],
                        'username' => $_POST['username'],
                        'password' => $_POST['password']
                    ]);
                } else
                {
                    // Execute user registration
                    $decodedUsername = html_entity_decode($_POST['username']);
                    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $registrationToken = $this->generateSecureRegistrationToken();

                    $sql = 'INSERT INTO users(username, email, password, registration_token) VALUES(?, ?, ?, ?)';
                    $query = $this->db->prepare($sql);
                    $query->bind_param('ssss',
                        $decodedUsername,
                        $_POST['email'],
                        $hashedPassword,
                        $registrationToken
                    );
                    $query->execute();
                    $result = $query->get_result();

                    Template::Render('login', [
                        'title' => "Login",
                        'notification' => [
                            'title' => 'Erfolg',
                            'message' => 'Der Account wurde erfolgreich angelegt.<br />Ihre Double-Opt-in-E-Mail finden Sie unter <a href="/register/email?userId='.$query->insert_id.'" class="formatted">diesem</a> Link.',
                            'icon' => 'icon_check_alt2'
                        ],
                        'email' => $_POST['email'],
                        'username' => $_POST['username'],
                        'password' => $_POST['password']
                    ]);
                }
            }

            print_r($result);
        } else
        {
            Template::Render('registration', [
                'title' => "Registrierung",
                'notification' => [
                    'title' => 'Fehler',
                    'message' => 'Bei der Registrierung ist ein Fehler aufgetreten. Bitte prüfen Sie Ihre Daten.',
                    'icon' => 'icon_error-circle_alt'
                ],
                'email' => $_POST['email'],
                'username' => $_POST['username'],
                'password' => $_POST['password']
            ]);
            die();
        }
    }

    public function showRegistrationEmail()
    {
        include(__DIR__.'/../config.php');

        if(isset($_GET['userId']))
        {
            $sql = 'SELECT username, email, registration_token FROM users WHERE id = ? LIMIT 1';
            $query = $this->db->prepare($sql);
            $query->bind_param('i', $_GET['userId']);
            $query->execute();
            $result = $query->get_result();
            $resultData = $result->fetch_array();

            Template::Render('registration_email', [
                'title' => "Double-Opt-in-E-Mail",
                'username' => $resultData['username'],
                'email' => $resultData['email'],
                'registrationToken' => $resultData['registration_token'],
                'host' => $config['host']
            ]);
        } else
        {
            // TODO: Add error handling on missing user id
        }
    }

    public function showFinishRegistration()
    {
        $this->redirectOnMissingAuthentication();

        Template::Render('registration_finish', [
            'title' => "Registrierung abschließen",
            'notification' => [
                'title' => 'Erfolg',
                'message' => 'Ihr Account wurde erfolgreich aktiviert. Bitte vervollständigen Sie Ihr Profil.',
                'icon' => 'icon_check_alt2'
            ]
        ]);
    }

    private function generateSecureRegistrationToken()
    {
        $randomBytes = openssl_random_pseudo_bytes(384);
        return hash('sha384', $randomBytes);
    }
}
