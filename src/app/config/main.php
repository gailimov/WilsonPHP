<?php

return array(
    'basePath' => __DIR__ . '/..',
    /*'namespace' => 'app',
    'modulesDir' => 'modules',
    'defaultModule' => 'main',
    'controllersDir' => 'controllers',
    'defaultController' => 'site'*/
    
    'routes' => array(
        'about' => array(
            'type' => 'static',
            'url' => 'about',
            'module' => 'main',
            'controller' => 'site',
            'action' => 'about'
        ),
        'greeting' => array(
            'type' => 'regex',
            'pattern' => '^hello/(?P<name>[-_a-z0-9]+)$',
            'module' => 'main',
            'controller' => 'site',
            'action' => 'greet'
        )
    ),
    
    /*'db' => array(
        'host' => '',
        'username' => '',
        'password' => '',
        'dbname' => '',
        'driver_options' => array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
    )*/
);
