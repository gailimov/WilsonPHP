<?php

namespace app\modules\main\controllers;

use wilson\Controller;

class Site extends Controller
{
    public function indexAction()
    {
        $this->render(array('message' => 'Welcome to the WilsonPHP!'));
    }
    
    public function aboutAction()
    {
        $this->renderPartial(array('message' => 'This is the site powered by WilsonPHP'));
    }
    
    public function greetAction()
    {
        $this->render(array('message' => 'Hello, ' . $this->getParam('name', 'World') . '!'));
    }
}
