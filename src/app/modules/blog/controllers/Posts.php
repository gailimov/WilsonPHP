<?php

namespace app\modules\blog\controllers;

use wilson\Controller;

class Posts extends Controller
{
    public function indexAction()
    {
        echo 'blog::Posts::index()';
    }
    
    public function showAction()
    {
        echo 'blog::Posts::index(). Param: ' . $this->getParam('name', 'default');
    }
}
