<?php

require_once __DIR__ . '/../../../../bootstrap.php';

use wilson\router\type\RegexRouter;

class RegexRouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \wilson\router\type\RegexRouter;
     */
    private $_router;
    
    /**
     * Routes
     * 
     * @var array
     */
    private $_routes = array(
        'article' => array(
            'type' => 'regex',
            'pattern' => '^article/(?P<slug>[-_a-z0-9]+)$',
            'module' => 'articles',
            'controller' => 'articles',
            'action' => 'show'
        )
    );
    
    public function setUp()
    {
        $this->_router = new RegexRouter();
    }
    
    public function testMatch()
    {
        $this->assertTrue($this->_router->match('article/something', $this->_routes));
        $this->assertFalse($this->_router->match('article/foo/bar', $this->_routes));
    }
    
    public function testGetActiveRouteName()
    {
        $this->assertEquals($this->_router->getActiveRouteName('article/something', $this->_routes), 'article');
        $this->assertFalse($this->_router->getActiveRouteName('article/foo/bar', $this->_routes));
    }
    
    public function testRoute()
    {
        $options = $this->_router->route('article', $this->_routes);
        $this->assertEquals(gettype($options), 'array');
        $this->assertEquals($options['module'], 'articles');
        $this->assertEquals($options['controller'], 'articles');
        $this->assertEquals($options['action'], 'show');
        $this->assertEquals(gettype($options['params']), 'array');
        $this->assertEquals($options['params'], $_GET);
    }
}
