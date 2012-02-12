<?php

use wilson\router\type\SegmentRouter;

class SegmentRouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var wilson\router\type\SegmentRouter
     */
    private $_router;
    
    /**
     * Routes
     * 
     * @var array
     */
    private $_routes = array(
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
        $this->_router = new SegmentRouter();
        $this->_router->addRoutes($this->_routes);
    }
    
    public function testMatch()
    {
        $this->assertTrue($this->_router->match('post/something'));
        $this->assertFalse($this->_router->match('post/foo/bar'));
    }
    
    public function testGetActiveRouteName()
    {
        $this->assertEquals($this->_router->getActiveRouteName('post/something'), 'post');
        $this->assertFalse($this->_router->getActiveRouteName('post/foo/bar'));
    }
    
    public function testRoute()
    {
        $options = $this->_router->route('post');
        $this->assertEquals(gettype($options), 'array');
        $this->assertEquals($options['module'], 'blog');
        $this->assertEquals($options['controller'], 'posts');
        $this->assertEquals($options['action'], 'show');
        $this->assertEquals(gettype($options['params']), 'array');
        $this->assertEquals($options['params'], $_GET);
    }
}
