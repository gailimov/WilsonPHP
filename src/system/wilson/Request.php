<?php

/**
 * WilsonPHP
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License v3
 */

namespace wilson;

/**
 * HTTP request class
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class Request
{
    /**
     * $_SERVER
     * 
     * @var array
     */
    private $_server = array();
    
    /**
     * Constructor
     * 
     * @param array $server $_SERVER
     */
    public function __construct(array $server = null)
    {
        if (!empty($server))
            $this->_server = $server;
        else
            $this->_server = $_SERVER;
    }
    
    /**
     * Returns $_SERVER value by key or If no key is passed, returns the entire $_SERVER array
     * 
     * @param  string $key     Key
     * @param  string $default Default value if key not exists
     * @return mixed
     */
    public function getServer($key = null, $default = null)
    {
        if (!$key)
            return $this->_server;
        
        if (isset($this->_server[(string) $key]))
            return $this->_server[(string) $key];
        return $default;
    }
    
    /**
     * Returns relative script URL
     * 
     * @return string
     */
    public function getScriptUrl()
    {
        return preg_replace('/^(.*?)index\.php$/is', '$1', $this->getServer('SCRIPT_NAME'));
    }
    
    /**
     * Returns base URL
     * 
     * @param  bool $absolute URL should be absolute?
     * @param  bool $https    Use HTTPS?
     * @return string
     */
    public function getBaseUrl($absolute = false, $https = false)
    {
        if (!$absolute)
            return rtrim($this->getScriptUrl(), '/');
        return $this->getHostInfo($https) . rtrim($this->getScriptUrl(), '/');
    }
    
    /**
     * Returns the scheme and host part of the URI
     * 
     * @param  bool $https Use HTTPS?
     * @return string
     */
    public function getHostInfo($https = false)
    {
        if ($https)
            return 'https://' . $this->getServer('HTTP_HOST');
        return 'http://' . $this->getServer('HTTP_HOST');
    }
}
