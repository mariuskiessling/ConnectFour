<?php

class MatchController extends BaseController {
    public function __construct()
    {
        parent::__construct();
    }

    public function showMatch()
    {
        if(isset($_GET['m']))
        {
            $sql = 'SELECT username FROM users WHERE id = ?';
            $query = $this->db->prepare($sql);
            $query->bind_param('i', $_SESSION['userId']);
            $query->execute();
            $userInformation = $query->get_result();

            // Check if current user is opponent or creator
            $sql = 'SELECT creator_id, opponent_id FROM matches
                WHERE public_id = ?';
            $query = $this->db->prepare($sql);
            $query->bind_param('s', $_GET['m']);
            $query->execute();
            $basicMatchInformation = $query->get_result();
            $basicMatchInformationData = $basicMatchInformation->fetch_array();

            if($basicMatchInformation->num_rows != 0)
            {
                if($basicMatchInformationData['creator_id'] == $_SESSION['userId'])
                {
                    $sql = 'SELECT matches.moves, color_schemes.description AS color_scheme, users.username AS opponent FROM matches
                        JOIN color_schemes ON matches.color_scheme_id = color_schemes.id
                        JOIN users ON matches.opponent_id = users.id
                        WHERE matches.public_id = ?';
                } else
                {
                    $sql = 'SELECT matches.moves, color_schemes.description AS color_scheme, users.username AS opponent FROM matches
                        JOIN color_schemes ON matches.color_scheme_id = color_schemes.id
                        JOIN users ON matches.creator_id = users.id
                        WHERE matches.public_id = ?';
                }

                $query = $this->db->prepare($sql);
                $query->bind_param('s', $_GET['m']);
                $query->execute();
                $matchInformation = $query->get_result();
                $matchInformationData = $matchInformation->fetch_array();

                if($basicMatchInformationData['creator_id'] == $_SESSION['userId'])
                {
                    $color = str_replace(' ', '', explode('/', $matchInformationData['color_scheme'])[0]);
                } else
                {
                    $color = str_replace(' ', '', explode('/', $matchInformationData['color_scheme'])[1]);
                }

                Template::Render('match', [
                    'title' => "Spiel",
                    'username' => $userInformation->fetch_array()['username'],
                    'opponent' => $matchInformationData['opponent'],
                    'moves' => $matchInformationData['moves'],
                    'color' => $color
                ]);

            } else
            {
                Template::Render('match_dialouge', [
                    'title' => "Fehler",
                    'username' => $userInformation->fetch_array()['username'],
                    'dialouge' => [
                        'icon' => 'icon_error-circle_alt',
                        'title' => 'Fehler',
                        'message' => 'Es ist ein Fehler aufgetreten. Dieses Spiel existiert nicht.',
                        'buttons' => [
                            [
                                'label' => 'Zur Lobby',
                                'link' => '/lobby'
                            ]
                        ]
                    ]
                ]);
            }
        }
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

    public function showMatchJoin()
    {
        $sql = 'SELECT username FROM users WHERE id = ?';
        $query = $this->db->prepare($sql);
        $query->bind_param('i', $_SESSION['userId']);
        $query->execute();
        $userInformation = $query->get_result();

        if(isset($_GET['qac']))
        {
            $sql = 'SELECT users.username, matches.public_id, matches.creator_id FROM matches
                JOIN users ON matches.creator_id = users.id
                WHERE matches.quick_access_code = ?';
            $query = $this->db->prepare($sql);
            $query->bind_param('s', $_GET['qac']);
            $query->execute();
            $matchInformation = $query->get_result();
            $matchInformationData = $matchInformation->fetch_array();

            if($matchInformation->num_rows != 0)
            {
                if($matchInformationData['creator_id'] == $_SESSION['userId'])
                {
                    header('Location: /match?m='.$matchInformationData['public_id']);
                }
                Template::Render('match_dialouge', [
                    'title' => "Lobby",
                    'username' => $userInformation->fetch_array()['username'],
                    'dialouge' => [
                        'icon' => 'icon_question_alt2',
                        'title' => 'Spiel beitreten',
                        'message' => 'Möchten Sie dem Spiel von <span class="italic">'.$matchInformationData['username'].'</span> beitreten?',
                        'buttons' => [
                            [
                                'label' => 'Ja, beitreten',
                                'link' => '/match/join/confirm?qac='.$_GET['qac']
                            ],
                            [
                                'label' => 'Abbrechen',
                                'link' => '/lobby'
                            ]
                        ]
                    ]
                ]);
            } else
            {
                Template::Render('match_dialouge', [
                    'title' => "Lobby",
                    'username' => $userInformation->fetch_array()['username'],
                    'dialouge' => [
                        'icon' => 'icon_error-circle_alt',
                        'title' => 'Spiel beitreten',
                        'message' => 'Das Spiel existiert nicht.',
                        'buttons' => [
                            [
                                'label' => 'Zur Lobby',
                                'link' => '/lobby'
                            ]
                        ]
                    ]
                ]);
            }
        } else
        {
            Template::Render('match_dialouge', [
                'title' => "Lobby",
                'username' => $userInformation->fetch_array()['username'],
                'dialouge' => [
                    'icon' => 'icon_error-circle_alt',
                    'title' => 'Spiel beitreten',
                    'message' => 'Das Spiel existiert nicht.',
                    'buttons' => [
                        [
                            'label' => 'Zur Lobby',
                            'link' => '/lobby'
                        ]
                    ]
                ]
            ]);
        }
    }

    public function joinMatch()
    {
        $sql = 'SELECT username FROM users WHERE id = ?';
        $query = $this->db->prepare($sql);
        $query->bind_param('i', $_SESSION['userId']);
        $query->execute();
        $userInformation = $query->get_result();

        if(isset($_GET['qac']))
        {
            $sql = 'UPDATE matches SET opponent_id = ? WHERE quick_access_code = ?';
            $query = $this->db->prepare($sql);
            $query->bind_param('is', $_SESSION['userId'], $_GET['qac']);
            $query->execute();

            if($query->affected_rows != 0)
            {
                $sql = 'SELECT public_id FROM matches WHERE quick_access_code = ?';
                $query = $this->db->prepare($sql);
                $query->bind_param('s', $_GET['qac']);
                $query->execute();
                $matchInformation = $query->get_result();

                header('Location: /match?m='.$matchInformation->fetch_array()['public_id']);
            } else
            {
                Template::Render('match_dialouge', [
                    'title' => "Lobby",
                    'username' => $userInformation->fetch_array()['username'],
                    'dialouge' => [
                        'icon' => 'icon_error-circle_alt',
                        'title' => 'Spiel beitreten',
                        'message' => 'Beim Beitreten ist ein Fehler aufgetreten.',
                        'buttons' => [
                            [
                                'label' => 'Zur Lobby',
                                'link' => '/lobby'
                            ]
                        ]
                    ]
                ]);
            }
        }
    }

    public function getMatchInformation()
    {
        if(isset($_GET['match_id']))
        {
            $sql = 'SELECT matches.field, matches.moves, matches.status, matches.creator_id,  matches.active_player_id, color_schemes.class as color_scheme_class FROM matches
                JOIN color_schemes ON matches.color_scheme_id = color_schemes.id
                WHERE public_id = ?';
            $query = $this->db->prepare($sql);
            $query->bind_param('s', $_GET['match_id']);
            $query->execute();
            $matchInformation = $query->get_result();
            $matchInformationData = $matchInformation->fetch_array();

            if($matchInformation->num_rows != 0)
            {
                echo json_encode([
                    'field' => $matchInformationData['field'],
                    'moves' => $matchInformationData['moves'],
                    'status' => $matchInformationData['status'],
                    'active' => $matchInformationData['active_player_id'] == $_SESSION['userId'],
                    'user_code' => $matchInformationData['creator_id'] == $_SESSION['userId'] ? 1 : 2,
                    'color_scheme_class' => $matchInformationData['color_scheme_class']
                ]);
            } else
            {
                http_response_code(400);
                echo json_encode([
                    'error' => 'Match does not exist.'
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

    public function makeMove()
    {
        if(isset($_POST['match_id']) && isset($_POST['field']))
        {
            $sql = 'SELECT field, moves, creator_id, opponent_id, active_player_id FROM matches WHERE public_id = ?';
            $query = $this->db->prepare($sql);
            $query->bind_param('s', $_POST['match_id']);
            $query->execute();
            $matchInformation = $query->get_result();

            if($matchInformation->num_rows != 0)
            {
                $matchInformationData = $matchInformation->fetch_array();
                $newMoves = $matchInformationData['moves'] + 1;

                if($_SESSION['userId'] == $matchInformationData['active_player_id'])
                {
                    if($_SESSION['userId'] == $matchInformationData['creator_id'])
                    {
                        $activePlayer = $matchInformationData['opponent_id'];
                    } elseif($_SESSION['userId'] == $matchInformationData['opponent_id'])
                    {
                        $activePlayer = $matchInformationData['creator_id'];
                    }

                    $sql = 'UPDATE matches SET field = ?, moves = ?, active_player_id = ? WHERE public_id = ?';
                    $query = $this->db->prepare($sql);
                    $query->bind_param('siis', $_POST['field'], $newMoves, $activePlayer, $_POST['match_id']);
                    $query->execute();
                    $matchInformation = $query->get_result();

                    echo json_encode([
                        'moves' => $newMoves
                    ]);
                } else
                {
                    // TODO: Add error handling
                }
            } else
            {
                http_response_code(400);
                echo json_encode([
                    'error' => 'Match does not exist.'
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
