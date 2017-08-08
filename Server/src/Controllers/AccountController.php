<?php

class AccountController extends BaseController {

    public function __construct()
    {
        parent::__construct();
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
                $sql = 'SELECT id FROM users WHERE registration_token = ?';
                $query = $this->db->prepare($sql);
                $query->bind_param('s', $_GET['token']);
                $query->execute();
                $userId = $query->get_result()->fetch_array()['id'];

                $_SESSION['userId'] = $userId;
                $_SESSION['clientUserAgent'] = $_SERVER['HTTP_USER_AGENT'];
                $_SESSION['clientIPAddress'] = $_SERVER['REMOTE_ADDR'];

                header('Location: /register/finish');
            }
        } else
        {
            // TODO: Add error handling on missing token
        }
    }

    public function updateInitialProfileInformation()
    {
        // Move profile picture if provided
        if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['tmp_name'] != '')
        {
            if(filesize($_FILES['profile_picture']['tmp_name']) <= 1000000
                && (exif_imagetype($_FILES['profile_picture']['tmp_name']) == IMAGETYPE_PNG
                || exif_imagetype($_FILES['profile_picture']['tmp_name']) == IMAGETYPE_JPEG))

            {
                $filetype = exif_imagetype($_FILES['profile_picture']['tmp_name']);
                if($filetype == IMAGETYPE_PNG)
                {
                    $filename = $this->generateSecure384Hash().'.png';
                }
                if($filetype == IMAGETYPE_JPEG)
                {
                    $filename = $this->generateSecure384Hash().'.jpg';
                }

                move_uploaded_file($_FILES['profile_picture']['tmp_name'], __DIR__.'/../../public/storage/profile_pictures/'.$filename);

                $sql = 'UPDATE users SET full_name = ?, birthday = ?, sex = ?, profile_picture_filename = ? WHERE id = ?';
                $query = $this->db->prepare($sql);

                $name = @$_POST['name'];
                $birthday = date('Y-m-d', strtotime(str_replace('.', '/', @$_POST['birthday'])));
                $sex = @$_POST['sex'];

                $query->bind_param('ssssi', $name, $birthday, $sex, $filename, $_SESSION['userId']);
                $query->execute();

                header('Location: /lobby');
            } else {
                Template::Render('registration_finish', [
                    'title' => "Registrierung abschließen",
                    'notification' => [
                        'title' => 'Fehler',
                        'message' => 'Das Profilbild darf nur im PNG- oder JPG-Format hochgeladen werden und darf max. 1MB groß sein.',
                        'icon' => 'icon_error-circle_alt'
                    ]
                ]);
            }
        } else
        {
            $sql = 'UPDATE users SET full_name = ?, birthday = ?, sex = ? WHERE id = ?';
            $query = $this->db->prepare($sql);

            $name = @$_POST['name'];
            $birthday = @$_POST['birthday'];
            $sex = @$_POST['sex'];

            $query->bind_param('sssi', $name, $birthday, $sex, $_SESSION['userId']);
            $query->execute();

            header('Location: /lobby');
        }
    }
}
