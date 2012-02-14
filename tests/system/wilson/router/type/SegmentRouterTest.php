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
        'admin' => array(
            'type' => 'segment',
            'url' => 'admin(/<controller>(/<action>(/<id>)))',
            'rules' => array(
                'id' => '\d+'
            ),
            'module' => 'blog',
            'directory' => 'admin',
            'controller' => 'posts',
            'action' => 'index'
        )
    );
    
    public function setUp()
    {
        $this->_router = new SegmentRouter();
        $this->_router->addRoutes($this->_routes);
    }
    
    public function testMatch()
    {
        $this->assertTrue($this->_router->match('admin/posts/edit/12'));
        $this->assertFalse($this->_router->match('admin/posts/edit/foo/bar'));
    }
    
    public function testGetActiveRouteName()
    {
        $this->assertEquals($this->_router->getActiveRouteName('admin/posts/edit/12'), 'admin');
        $this->assertFalse($this->_router->getActiveRouteName('admin/posts/edit/foo/bar'));
    }
    
    public function testRoute()
    {
        $options = $this->_router->route('admin');
        $this->assertEquals(gettype($options), 'array');
        $this->assertEquals($options['module'], 'blog');
        $this->assertEquals($options['directory'], 'admin');
        $this->assertEquals($options['controller'], 'posts');
        $this->assertEquals($options['action'], 'index');
        $this->assertEquals(gettype($options['params']), 'array');
        $this->assertEquals($options['params'], $_GET);
    }
    
    public function testCreateUrl()
    {
        $this->assertEquals($this->_router->createUrl('admin(/<controller>(/<action>(/<id>)))',
                                                       array('controller' => 'posts', 'action' => 'edit', 'id' => 12)),
                            'admin/posts/edit/12');
    }
}
