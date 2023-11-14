<?php

// Prepare a LANDO_INFO constant.
define('LANDO_INFO', json_decode($_ENV['LANDO_INFO'], TRUE));
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

$databases['default']['default'] = [
    'database' => 'migration',
    'username' => 'root',
    'password' => 'root',
    'host' => 'localhost',
    'port' => '3306',
    'driver' => 'mysql',
    'prefix' => '',
  ];

// Trusted host patterns.
$settings['hash_salt'] = "sdfsdsfd";
//$config['system.logging']['error_level']='verbose';
$settings['http_client_config']['force_ip_resolve'] = 'v4';


