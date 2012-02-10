<?php

/**
 * WilsonPHP
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License v3
 */

namespace wilson\router\type;

/**
 * Interface for routers
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
interface RouterInterface
{
    /**
     * Returns true if route matches to URI, otherwise returns false
     * 
     * @param  string $uri    URI
     * @param  array  $routes Routes
     * @return bool
     */
    public function match($uri, array $routes);
    
    /**
     * Returns active route's name if route matches to URI, otherwise returns false
     * 
     * @param  string $uri    URI
     * @param  array  $routes Routes
     * @return string || bool false
     */
    public function getActiveRouteName($uri, array $routes);
    
    /**
     * Routing
     * 
     * @param  string $name   Route name
     * @param  array  $routes Routes
     * @return array
     */
    public function route($name, array $routes);
}
