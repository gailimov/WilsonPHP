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
class RegexRouter implements RouterInterface
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
     *     $route = array(
     *         'article' => array(
     *             'type' => 'regex',
     *             'pattern' => '^article/(?P<slug>[-_a-z0-9]+)$',
     *             'module' => 'articles',
     *             'controller' => 'articles',
     *             'action' => 'show'
     *         )
     *     );
     *     if ($router->match('article/something', $route)) {
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
            if (preg_match('#' . $route['pattern'] . '#', (string) $uri, $matches))
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
            if (preg_match('#' . $route['pattern'] . '#', (string) $uri, $matches)) {
                foreach ($matches as $key => $value) {
                    if (preg_match('/[_a-zA-Z]/', $key))
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
                'action' => $routes[$name]['action'],
                'params' => $this->_params
            );
        }
        
        return array();
    }
}
