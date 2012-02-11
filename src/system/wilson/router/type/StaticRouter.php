<?php

/**
 * WilsonPHP
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License v3
 */

namespace wilson\router\type;

/**
 * Static router
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class StaticRouter extends RouterAbstract
{
    /**
     * Returns true if route matches to URI, otherwise returns false
     * 
     * Usage example:
     * 
     *     if ($router->match('sitemap')) {
     *         // ...
     *     }
     * 
     * @param  string $uri URI
     * @return bool
     */
    public function match($uri)
    {
        foreach ($this->_routes as $name => $route) {
            if ($route[self::URL_KEY] == (string) $uri)
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
            if ($route[self::URL_KEY] == (string) $uri)
                return $name;
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
            return $routes;
        }
        
        return array();
    }
}
