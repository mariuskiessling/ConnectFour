<?php

use \ConnectFour\Helpers\Router as Router;

Router::Request('GET', '/login', 'LoginController@showLogin');              // Called to show login
Router::Request('POST', '/login', 'LoginController@login');                 // Called to validate login

Router::Request('GET', '/register', 'LoginController@showRegistration');    // Called to show registration
Router::Request('POST', '/register', 'LoginController@register');           // Called to register a new user

Router::Request('GET', '/match', 'MatchController@showMatch');              // Called to show game ($_GET['id'] required)
Router::Request('GET', '/match/create', 'MatchController@showCreateMatch'); // Callled to create a new game
