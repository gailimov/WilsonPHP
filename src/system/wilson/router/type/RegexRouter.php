<?php

/**
 * WilsonPHP
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License v3
 */

namespace wilson\router\type;

/**
 * Regex router
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class RegexRouter extends RouterAbstract
{
    /**
     * Params
     * 
     * @var array
     */
    private $_params = array();
    
    /**
     * Returns true if route matches to URI, otherwise returns false
     * 
     * Usage example:
     * 
     *     if ($router->match('article/something')) {
     *         // ...
     *     }
     * 
     * @param  string $uri URI
     * @return bool
     */
    public function match($uri)
    {
        foreach ($this->_routes as $name => $route) {
            if (preg_match('#' . $route[self::URL_KEY] . '#', (string) $uri))
                return true;
        }
        
        return false;
    }
    
    /**
     * Returns active route's name if route matches to URI, otherwise returns false
     * 
     * @param  string $uri URI
     * @return string || bool false
     */
    public function getActiveRouteName($uri)
    {
        foreach ($this->_routes as $name => $route) {
            if (preg_match('#' . $route[self::URL_KEY] . '#', (string) $uri, $matches)) {
                foreach ($matches as $key => $value) {
                    // Skip all unnamed keys
                    if (is_int($key))
                        continue;
                    // Set the value for all matched keys
                    $this->_params[$key] = $value;
                }
                $this->_params = $_GET = array_merge($this->_params, $_GET);
                return $name;
            }
        }
        
        return false;
    }
    
    /**
     * Routing
     * 
     * @param  string $name Route name
     * @return array
     */
    public function route($name)
    {
        if (isset($this->_routes[(string) $name])) {
            $routes = $this->getRoute($name, array('module', 'controller', 'action'));
            $routes['params'] = $this->_params;
            return $routes;
        }
        
        return array();
    }
}
