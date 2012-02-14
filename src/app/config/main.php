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
            'url' => '^hello/(?P<name>[-_a-z0-9]+)$',
            'module' => 'main',
            'controller' => 'site',
            'action' => 'greet'
        ),*/
        'greeting' => array(
            'type' => 'segment',
            'url' => 'hello(/<name>)',
            'rules' => array(
                'name' => '[-_a-z0-9]+'
            ),
            'module' => 'main',
            'controller' => 'site',
            'action' => 'greet'
        ),
        'admin' => array(
            'type' => 'segment',
            'url' => '<directory>(/<controller>(/<action>(/<id>)))',
            'rules' => array(
                'id' => '\d+',
                'directory' => 'admin'
            ),
            'module' => 'blog',
            'controller' => 'posts'
        ),
        'default' => array(
            'type' => 'segment',
            'url' => '<controller>(/<action>(/<name>))',
            'module' => 'blog'
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
