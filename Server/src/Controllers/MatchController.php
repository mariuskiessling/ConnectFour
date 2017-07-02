<?php

class MatchController extends BaseController {
    public function __construct()
    {
        parent::__construct();
    }

    public function createMatch()
    {
        if(isset($_POST['color_scheme']))
        {
            // Check if given color scheme id exists in DB
            $sql = 'SELECT id FROM color_schemes WHERE id = ?';
            $query = $this->db->prepare($sql);
            $query->bind_param('s', $_POST['color_scheme']);
            $query->execute();
            $colorScheme = $query->get_result();

            if($colorScheme->num_rows != 0)
            {
                $quickAccessCode = '';
                $publicId = '';
                $field = '[[0,0,0,0,0,0,0],[0,0,0,0,0,0,0],[0,0,0,0,0,0,0],[0,0,0,0,0,0,0],[0,0,0,0,0,0,0],[0,0,0,0,0,0,0]]';
                $generateError = true;

                do
                {
                    $quickAccessCode = $this->generateQuickAccessCode();
                    $publicId = $this->generatePublicMatchId();

                    $sql = 'SELECT id FROM matches WHERE quick_access_code = ? OR public_id = ?';
                    $query = $this->db->prepare($sql);
                    $query->bind_param('ss', $quickAccessCode, $publicId);
                    $query->execute();

                    if($query->get_result()->num_rows == 0)
                    {
                        $generateError = false;
                    }
                } while($generateError);

                $sql = 'INSERT INTO matches(public_id, creator_id, quick_access_code, field, color_scheme_id, active_player_id)
                    VALUES(?, ?, ?, ?, ?, ?)';
                $query = $this->db->prepare($sql);
                $query->bind_param('sissii',
                    $publicId,
                    $_SESSION['userId'],
                    $quickAccessCode,
                    $field,
                    $_POST['color_scheme'],
                    $_SESSION['userId']
                );
                $query->execute();

                echo json_encode([
                    'public_id' => $publicId,
                    'quick_access_code' => $quickAccessCode
                ]);
            } else
            {
                http_response_code(400);
                echo json_encode([
                    'error' => 'Color scheme does not exist.'
                ]);
            }
        } else
        {
            http_response_code(400);
            echo json_encode([
                'error' => 'Missing parameters.'
            ]);
        }
    }

    private function generateQuickAccessCode($length = 5)
    {
        $dataset = '0123456789ABCDEFGHKLMNOPQRSTUVWXYZ';
        $quickAccessCode = '';

        for($i = 0; $i < $length; $i++)
        {
            $quickAccessCode = $quickAccessCode.$dataset[rand(0, strlen($dataset)-1)];
        }

        return $quickAccessCode;
    }

    private function generatePublicMatchId()
    {
        $randomBytes = openssl_random_pseudo_bytes(384);
        return hash('sha384', $randomBytes);
    }
}
