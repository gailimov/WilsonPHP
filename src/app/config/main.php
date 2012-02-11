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
        /*'greeting' => array(
            'type' => 'regex',
            'pattern' => '^hello/(?P<name>[-_a-z0-9]+)$',
            'module' => 'main',
            'controller' => 'site',
            'action' => 'greet'
        ),*/
        'greeting' => array(
            'type' => 'standartRegex',
            'uriPattern' => 'hello(/<name>)',
            'rules' => array(
                'name' => '[-_a-z0-9]+'
            ),
            'defaults' => array(
                'module' => 'main',
                'controller' => 'site',
                'action' => 'greet'
            )
        ),
        'default' => array(
            'type' => 'standartRegex',
            'uriPattern' => '<controller>(/<action>(/<name>))',
            'rules' => array(
                'module' => '[-_a-z]+'
            ),
            'defaults' => array(
                'module' => 'blog'
            )
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
