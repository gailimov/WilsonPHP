<?php

use wilson\router\StandartRouter;

class StandartRouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \wilson\router\StandartRouter;
     */
    private $_router;
    
    public function setUp()
    {
        $this->_router = new StandartRouter();
    }
    
    public function testRoute()
    {
        $options = $this->_router->route('index.php/module/controller/action/key1/value1/key2/value2');
        $this->assertEquals(gettype($options), 'array');
        $this->assertEquals($options['module'], 'module');
        $this->assertEquals($options['controller'], 'controller');
        $this->assertEquals($options['action'], 'action');
        $this->assertEquals(gettype($options['params']), 'array');
        $this->assertEquals(count($options['params']), 2);
        $this->assertEquals($options['params'], $_GET);
        $this->assertEquals($_GET['key2'], 'value2');
    }
    
    public function testParams()
    {
        try {
            $options = $this->_router->route('module/controller/action/param');
        } catch (Exception $e) {
            return;
        }
        $this->fail('Expected exception for non key-value params');
    }
}
