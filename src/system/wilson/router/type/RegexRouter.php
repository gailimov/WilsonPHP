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
     * Pattern of the URL
     */
    const REGEX_PATTERN = '^\?P?<(\w+)>(.*?)$';
    
    /**
     * Params
     * 
     * @var array
     */
    private $_params = array();
    
    /**
     * Returns type
     * 
     * @return string
     */
    public function getType()
    {
        return 'regex';
    }
    
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
    
    /**
     * Creates URL
     * 
     * Usage example:
     * 
     *     // Generates: /article/something
     *     $router->createUrl('^article/(?P<slug>[-_a-z0-9]+)$', array('slug' => 'something'));
     * 
     * @param  string $url    URL pattern
     * @param  array  $params Params
     * @return string
     */
    public function createUrl($url, array $params = null)
    {
        while (preg_match('#\([^()]++\)#', (string) $url, $matches)) {
            // Search for the matched value
            $search = $matches[0];
            // Remove the parenthesis from the match as the replace
            $replace = substr($matches[0], 1, -1);
            while (preg_match('#' . self::REGEX_PATTERN . '#', $replace, $matches)) {
                list($key, $param) = $matches;
                if (isset($params[$param])) {
                    // Replace the key with the parameter value
                    $replace = str_replace($key, $params[$param], $replace);
                } else {
                    // This group has missing parameters
                    $replace = '';
                    break;
                }
            }
            
            // Replace the group in the URL
            $url = str_replace($search, $replace, $url);
        }
        
        $url = str_replace('^', '', $url);
        $url = str_replace('$', '', $url);
        
        return preg_replace('#//+#', '/', rtrim($url, '/'));
    }
}
