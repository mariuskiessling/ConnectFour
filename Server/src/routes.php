<?php

use \ConnectFour\Helpers\Router as Router;

Router::Request('GET', '/login', 'LoginController@showLogin');              // Called to show login
Router::Request('POST', '/login', 'LoginController@login');                 // Called to validate login
Router::Request('GET', '/logout', 'LoginController@logout');

Router::Request('GET', '/register', 'RegisterController@showRegistration');    // Called to show registration
Router::Request('POST', '/register', 'RegisterController@register');           // Called to register a new user
Router::Request('GET', '/register/email', 'RegisterController@showRegistrationEmail');     // Called to show double-opt-in email (Just for showcase)
Router::Request('GET', '/register/confirm', 'AccountController@activateAccount');           // Called to activate a new user
Router::Request('GET', '/register/finish', 'RegisterController@showFinishRegistration');
Router::Request('POST', '/register/finish', 'AccountController@updateInitialProfileInformation');

Router::Request('GET', '/match', 'MatchController@showMatch');              // Called to show game ($_GET['id'] required)
Router::Request('POST', '/match/create', 'MatchController@createMatch');// Callled to create a new game
Router::Request('GET', '/match/join', 'MatchController@showMatchJoin');
Router::Request('GET', '/match/join/confirm', 'MatchController@joinMatch');
Router::Request('GET', '/match/update', 'MatchController@getMatchInformation');
Router::Request('POST', '/match/make_move', 'MatchController@makeMove');
Router::Request('GET', '/match/surrender', 'MatchController@surrender');

Router::Request('GET', '/lobby', 'LobbyController@showLobby');              // Called to show user an interface to create new matches
Router::Request('GET', '/lobby/public_matches_partial', 'LobbyController@getPublicMatchesPartial');
