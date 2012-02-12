<?php

/**
 * WilsonPHP
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License v3
 */

namespace wilson\router;

use wilson\Request,
    wilson\router\type\RouterInterface;

/**
 * Router
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class Router
{
    /**
     * Type key
     */
    const TYPE_KEY = 'type';
    
    /**
     * Static router type
     */
    const TYPE_STATIC = 'static';
    
    /**
     * Regex router type
     */
    const TYPE_REGEX = 'regex';
    
    /**
     * Segment router type
     */
    const TYPE_SEGMENT = 'segment';
    
    /**
     * URL key
     */
    const URL_KEY = 'url';
    
    /**
     * Routes
     * 
     * @var array
     */
    private $_routes = array();
    
    /**
     * Router types
     * 
     * @var array
     */
    private $_types = array();
    
    /**
     * @var \wilson\Request
     */
    private $_request;
    
    /**
     * URI
     * 
     * @var string
     */
    private $_uri;
    
    /**
     * Adds route
     * 
     * Usage example:
     * 
     *     $router->addRoute('article', array(
     *         'type' => 'regex',
     *         'pattern' => '^article/(?P<slug>[-_a-z0-9]+)$',
     *         'module' => 'articles',
     *         'controller' => 'articles',
     *         'action' => 'show'
     *     ));
     * 
     * @param  string $name  Name
     * @param  array  $route Route
     * @return wilson\router\Router
     */
    public function addRoute($name, array $route)
    {
        $this->_routes[(string) $name] = $route;
        return $this;
    }
    
    /**
     * Adds routes
     * 
     * Usage example:
     * 
     *     $routes = array(
     *         'sitemap' => array(
     *             'type' => 'static',
     *             'url' => 'sitemap',
     *             'controller' => 'site',
     *             'action' => 'sitemap'
     *         ),
     *         'article' => array(
     *             'type' => 'regex',
     *             'pattern' => '^article/(?P<slug>[-_a-z0-9]+)$',
     *             'module' => 'articles',
     *             'controller' => 'articles',
     *             'action' => 'show'
     *         )
     *     );
     *     $router->addRoutes($routes);
     * 
     * @param  array $routes Routes
     * @return wilson\router\Router
     */
    public function addRoutes(array $routes)
    {
        foreach ($routes as $name => $route)
            $this->addRoute((string) $name, (array) $route);
        return $this;
    }
    
    /**
     * Returns routes
     * 
     * @return array
     */
    public function getRoutes()
    {
        return $this->_routes;
    }
    
    /**
     * Adds router type
     * 
     * Usage example:
     * 
     *     $router->addType(new StaticRouter());
     * 
     * @param  wilson\router\type\RouterInterface $type Type
     * @return wilson\router\Router
     */
    public function addType(RouterInterface $type)
    {
        $this->_types[$type->getType()] = $type;
        return $this;
    }
    
    /**
     * Adds routers types
     * 
     * Usage example:
     * 
     *     $router->addTypes(array(
     *         new StaticRouter(),
     *         new RegexRouter(),
     *         new SegmentRouter()
     *     ));
     * 
     * @param  array $types Types
     * @return wilson\router\Router
     */
    public function addTypes(array $types)
    {
        foreach ($types as $type)
            $this->addType($type);
        return $this;
    }
    
    /**
     * Returns router by type
     * 
     * @param  string $type Type
     * @return wilson\router\type\RouterInterface
     */
    public function getRouterByType($type)
    {
        return $this->_types[(string) $type];
    }
    
    /**
     * Creates URL
     * 
     * Usage example:
     * 
     *     // Generates: /article/something
     *     $router->createUrl('article', array('slug' => 'something'));
     *     // Generates: https://example.com/article/something
     *     $router->createUrl('article', array('slug' => 'something'), true, true);
     * 
     * @param  string $name     Route name
     * @param  array  $params   Params
     * @param  bool   $absolute URL should be absolute?
     * @param  bool   $https    Use HTTPS?
     * @return string
     */
    public function createUrl($name, array $params = null, $absolute = false, $https = false)
    {
        foreach ($this->_routes as $routeName => $route) {
            if ($routeName == $name) {
                $replacement = ($params) ? '%s' : '';
                // Static route
                if ($route[self::TYPE_KEY] == self::TYPE_STATIC) {
                    $url = $route[self::URL_KEY];
                // Regex route
                } else {
                    /** @TODO: Пофиксить, чтобы заменялись только именованный параметры, с соотвествующим ключем в params */
                    $url = preg_replace('/\([^\)]*\)/', $replacement, $route[self::URL_KEY]);
                    $url = str_replace('^', '', $url);
                    $url = str_replace('$', '', $url);
                }
                
                if (!$absolute) {
                    if ($params)
                        return $this->_request->getScriptUrl() . vsprintf($url, $params);
                    return $this->_request->getScriptUrl() . $url;
                } else {
                    return $this->_request->getHostInfo($https) . $this->createUrl($name, $params);
                }
            }
        }
        
        // Nothing found - return base URL
        if (!$absolute)
            return $this->_request->getBaseUrl();
        return $this->_request->getBaseUrl(true);
    }
    
    /**
     * Running
     * 
     * @param  object \wilson\Request
     * @return array
     */
    public function run(Request $request)
    {
        $this->_request = $request;
        $this->_uri = $this->getUri($request);
        
        return $this->route();
    }
    
    /**
     * Returns URI
     * 
     * @param  object \wilson\Request
     * @return string
     */
    private function getUri(Request $request)
    {
        // Necessarry if site locates in subdirectory
        $uri = $request->getScriptUrl();
        $uri = preg_replace('/^' . preg_quote($uri, '/') . '/is', '', urldecode($request->getServer('REQUEST_URI')));
        $uri = preg_replace('/(\/?)(\?.*)?$/is', '', $uri);
        // Cut unecessary symbols
        $uri = preg_replace('/[^0-9A-Za-z._\\-\\/]is/', '', $uri);
        
        return $uri;
    }
    
    /**
     * Returns routes by type
     * 
     * @param  string $type Router type
     * @return array
     */
    private function getRoutesByType($type)
    {
        $routes = array();
        
        foreach ($this->_routes as $name => $route) {
            if ($route[self::TYPE_KEY] == (string) $type) {
                if (isset($route[self::URL_KEY]))
                    $routes[$name] = $route;
            }
        }
        
        return $routes;
    }
    
    /**
     * Routing
     * 
     * @return array
     */
    private function route()
    {
        foreach ($this->_routes as $name => $route) {
            $router = $this->getRouterByType($route[self::TYPE_KEY]);
            $routes = $this->getRoutesByType($router->getType());
            $router->addRoutes($routes);
            if ($router->match($this->_uri))
                return $router->route($router->getActiveRouteName($this->_uri));
        }
        
        // Nothing matched - standart routing
        $router = new StandartRouter();
        return $router->route($this->_uri);
    }
}
