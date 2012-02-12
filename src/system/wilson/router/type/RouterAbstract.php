<?php

/**
 * WilsonPHP
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License v3
 */

namespace wilson\router\type;

/**
 * Abstract class for routers
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
abstract class RouterAbstract implements RouterInterface
{
    /**
     * URL key
     */
    const URL_KEY = 'url';
    
    /**
     * Routes
     * 
     * @var array
     */
    protected $_routes = array();
    
    /**
     * Adds routes
     * 
     * @param  array $routes Routes
     * @return wilson\router\type\RouterAbstract
     */
    public function addRoutes(array $routes)
    {
        $this->_routes = $routes;
    }
    
    /**
     * Returns routes by keys
     * 
     * @param  string $name Route name
     * @param  array  $keys Keys
     * @return array
     */
    protected function getRoute($name, array $keys)
    {
        $routes = array();
        $length = count($keys);
        for ($i = 0; $i < $length; $i++) {
            if (isset($this->_routes[$name][$keys[$i]]))
                $routes[$keys[$i]] = $this->_routes[$name][$keys[$i]];
        }
        
        return $routes;
    }
}
