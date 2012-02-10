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
class StaticRouter implements RouterInterface
{
    /**
     * Returns true if route matches to URI, otherwise returns false
     * 
     * Usage example:
     * 
     *     $route = array(
     *         'sitemap' => array(
     *             'type' => 'static',
     *             'url' => 'sitemap',
     *             'controller' => 'site',
     *             'action' => 'sitemap'
     *         )
     *     );
     *     if ($router->match('sitemap', $route)) {
     *         // ...
     *     }
     * 
     * @param  string $uri    URI
     * @param  array  $routes Routes
     * @return bool
     */
    public function match($uri, array $routes)
    {
        foreach ($routes as $name => $route) {
            if ($route['url'] == (string) $uri)
                return true;
        }
        
        return false;
    }
    
    /**
     * Returns active route's name if route matches to URI, otherwise returns false
     * 
     * @param  string $uri    URI
     * @param  array  $routes Routes
     * @return string || bool false
     */
    public function getActiveRouteName($uri, array $routes)
    {
        foreach ($routes as $name => $route) {
            if ($route['url'] == (string) $uri)
                return $name;
        }
        
        return false;
    }
    
    /**
     * Routing
     * 
     * @param  string $name   Route name
     * @param  array  $routes Routes
     * @return array
     */
    public function route($name, array $routes)
    {
        if (isset($routes[(string) $name])) {
            return array(
                'module' => $routes[$name]['module'],
                'controller' => $routes[$name]['controller'],
                'action' => $routes[$name]['action']
            );
        }
        
        return array();
    }
}
