<?php

/**
 * WilsonPHP
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License v3
 */

namespace wilson\router;

/**
 * Standart regex router
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class StandartRegexRouter
{
    /**
     * Pattern of a <segment>
     */
    const REGEX_KEY = '<([a-zA-Z0-9_]++)>';
    
    /**
     * What can be part of a <segment> value
     */
    const REGEX_SEGMENT = '[^/.,;?\n]++';
    
    /**
     * What must be escaped in the route regex
     */
    const REGEX_ESCAPE = '[.\\+*?[^\\]${}=!|]';
    
    /**
     * Routes
     * 
     * @var array
     */
    private $_routes = array();
    
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
     *             'type' => 'standartRegex',
     *             'uriPattern' => '<module>/<controller>/<action>/<id>'
     *         )
     *     );
     *     if ($router->match('blog/post/show/22', $route)) {
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
            $rules = isset($route['rules']) ? $route['rules'] : null;
            $regex = $this->compile($route['uriPattern'], $rules);
            if (preg_match($regex, $uri))
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
            $rules = isset($route['rules']) ? $route['rules'] : null;
            $regex = $this->compile($route['uriPattern'], $rules);
            if (preg_match($regex, $uri, $matches)) {
                foreach ($matches as $key => $value) {
                    // Skip all unnamed keys
                    if (is_int($key))
                        continue;
                    // Set the value for all matched keys
                    $this->setRoutes($name, $matches);
                    $this->_params[$key] = $value;
                }
                
                if (isset($route['defaults'])) {
                    // Set default values for any key that was not matched
                    foreach ($route['defaults'] as $key => $value) {
                        if (!isset($this->_routes[$name][$key]) || $this->_routes[$name][$key] === '')
                            $this->_routes[$name][$key] = $value;
                    }
                }
                
                // Remove module, controller, action from params
                $this->removeUnnecessaryParams(array('module', 'controller', 'action'));
                
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
     * Returns the compiled regular expression for the route. This translates
     * keys and optional groups to a proper PCRE regular expression.
     * 
     * Usage example:
     * 
     *     $compiled = $router->compile('<controller>/<action>/<id>', array(
     *         'controller' => '[-_a-z]+',
     *         'id' => '\d+'
     *     ));
     * 
     * @param  string $pattern URI pattern
     * @return string
     */
    private function compile($pattern, array $regex = null)
    {
        // The URI should be considered literal except for keys and optional parts
        // Escape everything preg_quote would escape except for : ( ) < >
        $expression = preg_replace('#' . self::REGEX_ESCAPE . '#', '\\\\$0', (string) $pattern);
        
        if (strpos($expression, '(') !== false) {
            // Make optional parts of the URI non-capturing and optional
            $expression = str_replace(array('(', ')'), array('(?:', ')?'), $expression);
        }
        
        // Insert default regex for keys
        $expression = str_replace(array('<', '>'), array('(?P<', '>' . self::REGEX_SEGMENT . ')'), $expression);
        
        if ($regex) {
            $search = $replace = array();
            foreach ($regex as $key => $value) {
                $search[]  = '<' . $key . '>' . self::REGEX_SEGMENT;
                $replace[] = '<' . $key . '>' . $value;
            }
            // Replace the default regex with the user-specified regex
            $expression = str_replace($search, $replace, $expression);
        }
        
        return '#^' . $expression . '$#uD';
    }
    
    /**
     * Sets routes
     * 
     * @param  string $name   Name
     * @param  array  $routes Routes
     * @return void
     */
    private function setRoutes($name, array $routes)
    {
        $this->setRoutesIfKeyExists($name, array('module', 'controller', 'action'), $routes);
    }
    
    /**
     * Set routes if key exists
     * 
     * @param array $keys   Routes keys
     * @param array $routes Routes
     */
    public function setRoutesIfKeyExists($name, array $keys, array $routes)
    {
        $length = count($keys);
        for ($i = 0; $i < $length; $i++) {
            if (isset($routes[$keys[$i]]))
                $this->_routes[$name][$keys[$i]] = $routes[$keys[$i]];
        }
    }
    
    /**
     * Removes unnecessary parameters
     * 
     * @param  array $keys Params keys
     * @return array
     */
    private function removeUnnecessaryParams(array $keys)
    {
        $length = count($keys);
        for ($i = 0; $i < $length; $i++) {
            if (isset($this->_params[$keys[$i]]))
                unset($this->_params[$keys[$i]]);
        }
    }
    
    /**
     * Returns routes by keys
     * 
     * @param  string $name Route name
     * @param  array  $keys Keys
     * @return array
     */
    private function getRoute($name, array $keys)
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
