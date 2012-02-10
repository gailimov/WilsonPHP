<?php

use wilson\Request;

class RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \wilson\Request
     */
    private $_request;
    
    public function setUp()
    {
        $this->_request = new Request(array(
            'HTTP_HOST' => 'localhost',
            'REQUEST_URI' => '/site/index',
            'SCRIPT_NAME' => '/index.php',
        ));
    }
    
    public function testGetServer()
    {
        $this->assertEquals(gettype($this->_request->getServer()), 'array');
        $this->assertEquals(count($this->_request->getServer()), 3);
        $this->assertEquals($this->_request->getServer('REQUEST_URI'), '/site/index');
        $this->assertEquals($this->_request->getServer('foo'), null);
        $this->assertEquals($this->_request->getServer('foo', 'bar'), 'bar');
        
        $request = new Request();
        $this->assertEquals($request->getServer(), $_SERVER);
    }
    
    public function testGetScriptUrl()
    {
        $this->assertEquals($this->_request->getScriptUrl(), '/');
    }
    
    public function testGetBaseUrl()
    {
        $this->assertEquals($this->_request->getBaseUrl(), '');
        $this->assertEquals($this->_request->getBaseUrl(true, true), 'https://localhost');
    }
    
    public function testGetHostInfo()
    {
        $this->assertEquals($this->_request->getHostInfo(), 'http://localhost');
        $this->assertEquals($this->_request->getHostInfo(true), 'https://localhost');
    }
}
