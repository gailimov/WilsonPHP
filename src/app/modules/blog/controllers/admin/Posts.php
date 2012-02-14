<?php

namespace app\modules\blog\controllers\admin;

use wilson\Controller;

class Posts extends Controller
{
    public function indexAction()
    {
        echo 'blog::admin/Posts::index';
    }
    
    public function showAction()
    {
        echo 'blog::admin/Posts::show(' . $_GET['id'] . ')';
    }
}
