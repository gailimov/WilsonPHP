<?php

use wilson\router\Router,
    wilson\router\Exception,
    wilson\router\type\StaticRouter,
    wilson\router\type\RegexRouter,
    wilson\router\type\SegmentRouter,
    wilson\Request;

class RouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \wilson\router\Router
     */
    private $_router;
    
    /**
     * Routes
     * 
     * @var array
     */
    private $_routes = array(
        'sitemap' => array(
            'type' => 'static',
            'url' => 'sitemap',
            'module' => 'main',
            'controller' => 'site',
            'action' => 'sitemap'
        ),
        'article' => array(
            'type' => 'regex',
            'url' => '^article/(?P<slug>[-_a-z0-9]+)$',
            'module' => 'articles',
            'controller' => 'articles',
            'action' => 'show'
        ),
        'post' => array(
            'type' => 'segment',
            'url' => 'post/<slug>',
            'rules' => array(
                'slug' => '[-_a-z0-9]+'
            ),
            'module' => 'blog',
            'controller' => 'posts',
            'action' => 'show'
        )
    );
    
    public function setUp()
    {
        $this->_router = new Router();
        $this->_router->addTypes(array(
            new StaticRouter(),
            new RegexRouter(),
            new SegmentRouter()
        ));
    }
    
    public function testAddRoute()
    {
        $this->_router
            ->addRoute('sitemap', array(
                'type' => 'static',
                'url' => 'sitemap',
                'module' => 'main',
                'controller' => 'site',
                'action' => 'sitemap'
            ))
            ->addRoute('article', array(
                'type' => 'regex',
                'url' => '^article/(?P<slug>[-_a-z0-9]+)$',
                'module' => 'articles',
                'controller' => 'articles',
                'action' => 'show'
            ))
            ->addRoute('post', array(
                'type' => 'segment',
                'url' => 'post/<slug>',
                'rules' => array(
                    'slug' => '[-_a-z0-9]+'
                ),
                'module' => 'blog',
                'controller' => 'posts',
                'action' => 'show'
            ));
        
        $this->assertEquals($this->_router->getRoutes(), $this->_routes);
    }
    
    public function testAddRoutes()
    {
        $this->_router->addRoutes($this->_routes);
        $this->assertEquals($this->_router->getRoutes(), $this->_routes);
    }
    
    public function testCreateUrl()
    {
        $this->_router->addRoutes($this->_routes)->run(new Request(array(
            'HTTP_HOST' => 'localhost',
            'SCRIPT_NAME' => '/'
        )));
        $this->assertEquals($this->_router->createUrl('sitemap'), '/sitemap');
        $this->assertEquals($this->_router->createUrl('sitemap', null, true, true), 'https://localhost/sitemap');
        $this->assertEquals($this->_router->createUrl('article', array('slug' => 'something')), '/article/something');
        $this->assertEquals($this->_router->createUrl('post', array('slug' => 'something')), '/post/something');
    }
    
    public function testStandartRouter()
    {
        $options = $this->_router->run(new Request(array(
            'SCRIPT_NAME' => '/',
            'REQUEST_URI' => 'module/controller/action/key1/value1/key2/value2'
        )));
        $this->assertEquals(gettype($options), 'array');
        $this->assertEquals($options['module'], 'module');
        $this->assertEquals($options['controller'], 'controller');
        $this->assertEquals($options['action'], 'action');
        $this->assertEquals(gettype($options['params']), 'array');
        $this->assertTrue(count($options['params']) % 2 == 0);
        $this->assertEquals($options['params'], $_GET);
        $this->assertEquals($_GET['key2'], 'value2');
    }
    
    public function testStaticRouter()
    {
        $options = $this->_router->addRoutes($this->_routes)->run(new Request(array(
            'SCRIPT_NAME' => '/',
            'REQUEST_URI' => 'sitemap'
        )));
        $this->assertEquals(gettype($options), 'array');
        $this->assertEquals($options['module'], 'main');
        $this->assertEquals($options['controller'], 'site');
        $this->assertEquals($options['action'], 'sitemap');
    }
    
    public function testRegexRouter()
    {
        $options = $this->_router->addRoutes($this->_routes)->run(new Request(array(
            'SCRIPT_NAME' => '/',
            'REQUEST_URI' => 'article/something'
        )));
        $this->assertEquals(gettype($options), 'array');
        $this->assertEquals($options['module'], 'articles');
        $this->assertEquals($options['controller'], 'articles');
        $this->assertEquals($options['action'], 'show');
        $this->assertEquals(gettype($options['params']), 'array');
        $this->assertEquals($options['params'], $_GET);
        $this->assertEquals($_GET['slug'], 'something');
    }
    
    public function testSegmentRouter()
    {
        $options = $this->_router->addRoutes($this->_routes)->run(new Request(array(
            'SCRIPT_NAME' => '/',
            'REQUEST_URI' => 'post/something'
        )));
        $this->assertEquals(gettype($options), 'array');
        $this->assertEquals($options['module'], 'blog');
        $this->assertEquals($options['controller'], 'posts');
        $this->assertEquals($options['action'], 'show');
        $this->assertEquals(gettype($options['params']), 'array');
        $this->assertEquals($options['params'], $_GET);
        $this->assertEquals($_GET['slug'], 'something');
    }
    
    public function testParams()
    {
        try {
            $options = $this->_router->run(new Request(array(
                'SCRIPT_NAME' => '/',
                'REQUEST_URI' => 'module/controller/action/param'
            )));
        } catch (Exception $e) {
            return;
        }
        $this->fail('Expected exception for non key-value params');
    }
}
