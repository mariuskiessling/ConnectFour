<?php

use \ConnectFour\Helpers\Router as Router;

Router::Request('GET', '/hello', 'LoginController@sayHello', ['name' => 'Marius']);
