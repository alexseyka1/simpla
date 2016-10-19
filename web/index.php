<?php

require '../Loader.php';
define('BASE_URL', '/var/www/html');
$loader = new \Simpla\Loader();
$loader->register();

$loader->addNamespace('Simpla\Core', BASE_URL . '/app/core/src');
$loader->addNamespace('Simpla\Core\Classes', BASE_URL . '/app/core/src/classes');

$config = require(BASE_URL . '/app/config/config.php');
$app = new \Simpla\Core\Simpla;

try {
    $app->run($config);
}catch (Exception $e){
    echo $e->getMessage();
}