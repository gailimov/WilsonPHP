<?php

/**
 * WilsonPHP
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License v3
 */

namespace wilson\router\type;

/**
 * Segment router
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class SegmentRouter extends RouterAbstract
{
    /**
     * What can be part of a <segment> value
     */
    const REGEX_SEGMENT = '[^/.,;?\n]++';
    
    /**
     * What must be escaped in the route regex
     */
    const REGEX_ESCAPE = '[.\\+*?[^\\]${}=!|]';
    
    /**
     * Rules key
     */
    const RULES_KEY = 'rules';
    
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
        return 'segment';
    }
    
    /**
     * Returns true if route matches to URI, otherwise returns false
     * 
     * Usage example:
     * 
     *     if ($router->match('articles/show/something')) {
     *         // ...
     *     }
     * 
     * @param  string $uri URI
     * @return bool
     */
    public function match($uri)
    {
        foreach ($this->_routes as $name => $route) {
            if (preg_match($this->compile($route[self::URL_KEY], isset($route[self::RULES_KEY]) ? $route[self::RULES_KEY] : null), $uri))
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
            $regex = $this->compile($route[self::URL_KEY], isset($route[self::RULES_KEY]) ? $route[self::RULES_KEY] : null);
            if (preg_match($regex, $uri, $matches)) {
                foreach ($matches as $key => $value) {
                    // Skip all unnamed keys
                    if (is_int($key))
                        continue;
                    // Set the value for all matched keys
                    $this->setRoutes($name, $matches);
                    $this->_params[$key] = $value;
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
     * @param  string $segments URI segments
     * @param  array  $rules    Regex rules
     * @return string
     */
    private function compile($segments, array $rules = null)
    {
        // The URI should be considered literal except for keys and optional parts
        // Escape everything preg_quote would escape except for : ( ) < >
        $expression = preg_replace('#' . self::REGEX_ESCAPE . '#', '\\\\$0', (string) $segments);
        
        // Make optional parts of the URI non-capturing and optional
        if (strpos($expression, '(') !== false)
            $expression = str_replace(array('(', ')'), array('(?:', ')?'), $expression);
        
        // Insert default regex for keys
        $expression = str_replace(array('<', '>'), array('(?P<', '>' . self::REGEX_SEGMENT . ')'), $expression);
        
        if ($rules) {
            $search = $replace = array();
            foreach ($rules as $key => $value) {
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
    private function setRoutesIfKeyExists($name, array $keys, array $routes)
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
}
