<?php

class LobbyController extends BaseController {
    public function __construct()
    {
        parent::__construct();
    }

    public function showLobby()
    {
        $this->redirectOnMissingAuthentication();

        include(__DIR__.'/../config.php');

        // User information
        $sql = 'SELECT username FROM users WHERE id = ?';
        $query = $this->db->prepare($sql);
        $query->bind_param('i', $_SESSION['userId']);
        $query->execute();
        $userInformation = $query->get_result();

        // The users matches
        // TODO: Add selection of user games that were not created by this user
        $sql = 'SELECT matches.creator_id, matches.opponent_id, matches.public_id, creator_users.username AS creator_username, opponent_users.username AS opponent_username, matches.quick_access_code, matches.status, matches.created_at
            FROM matches
            LEFT JOIN users creator_users ON matches.creator_id = creator_users.id
            LEFT JOIN users opponent_users ON matches.opponent_id = opponent_users.id
            WHERE creator_id = ?
            OR opponent_id = ?
            ORDER BY matches.created_at DESC';

        $query = $this->db->prepare($sql);
        $query->bind_param('ii', $_SESSION['userId'], $_SESSION['userId']);
        $query->execute();
        $userMatches = $query->get_result();

        // Available color schemes
        $sql = 'SELECT id, description, class FROM color_schemes';
        $query = $this->db->prepare($sql);
        $query->execute();
        $colorSchemes = $query->get_result();

        // List of all open matches
        $sql = 'SELECT users.username AS creator, matches.quick_access_code, matches.created_at
            FROM matches
            LEFT JOIN users ON matches.creator_id = users.id
            WHERE matches.opponent_id IS NULL
            AND creator_id <> ?';

        $query = $this->db->prepare($sql);
        $query->bind_param('i', $_SESSION['userId']);
        $query->execute();
        $openMatches = $query->get_result();

        Template::Render('lobby', [
            'title' => "Lobby",
            'username' => $userInformation->fetch_array()['username'],
            'userMatches' => $userMatches,
            'colorSchemes' => $colorSchemes,
            'openMatches' => $openMatches,
            'host' => $config['host']
        ]);
    }
}
