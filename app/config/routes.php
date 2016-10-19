<?php

use Simpla\Core\Classes\Route;
use Simpla\Core\Router;

$router = new Router();

return [
    new Route(
        'GET',
        '/',
        $router->page(BASE_URL . '/app/views/book', ['args' => [1,2,3] ])
    ),
    new Route(
        'GET',
        '/book/:id',
        function($args){
            echo 'Hello, Dolly! Its a book #'.$args['id'].' page!';
        }
    ),
];