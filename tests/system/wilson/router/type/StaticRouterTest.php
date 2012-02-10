<?php

require_once __DIR__ . '/../../../../bootstrap.php';

use wilson\router\type\StaticRouter;

class StaticRouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \wilson\router\type\StaticRouter;
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
        )
    );
    
    public function setUp()
    {
        $this->_router = new StaticRouter();
    }
    
    public function testMatch()
    {
        $this->assertTrue($this->_router->match('sitemap', $this->_routes));
        $this->assertFalse($this->_router->match('foo', $this->_routes));
    }
    
    public function testGetActiveRouteName()
    {
        $this->assertEquals($this->_router->getActiveRouteName('sitemap', $this->_routes), 'sitemap');
        $this->assertFalse($this->_router->getActiveRouteName('foo', $this->_routes));
    }
    
    public function testRoute()
    {
        $options = $this->_router->route('sitemap', $this->_routes);
        $this->assertEquals(gettype($options), 'array');
        $this->assertEquals($options['module'], 'main');
        $this->assertEquals($options['controller'], 'site');
        $this->assertEquals($options['action'], 'sitemap');
    }
}