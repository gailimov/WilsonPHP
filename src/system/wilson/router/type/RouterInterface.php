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
     * @param  string $uri URI
     * @return bool
     */
    public function match($uri);
    
    /**
     * Returns active route's name if route matches to URI, otherwise returns false
     * 
     * @param  string $uri URI
     * @return string || bool false
     */
    public function getActiveRouteName($uri);
    
    /**
     * Routing
     * 
     * @param  string $name Route name
     * @return array
     */
    public function route($name);
}
