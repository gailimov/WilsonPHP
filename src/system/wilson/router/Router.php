<?php

/**
 * WilsonPHP
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License v3
 */

namespace wilson\router;

use wilson\Request,
    wilson\router\type\RouterInterface,
    wilson\router\type\StaticRouter,
    wilson\router\type\RegexRouter;

/**
 * Router
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class Router
{
    /**
     * Routes
     * 
     * @var array
     */
    private $_routes = array();
    
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
     * Router instance
     * 
     * @var object
     */
    private $_router;
    
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
                if (isset($route['url'])) {
                    $url = $route['url'];
                // Regex route
                } else {
                    /** @TODO: Пофиксить, чтобы заменялись только именованный параметры, с соотвествующим ключем в params */
                    $url = preg_replace('/\([^\)]*\)/', $replacement, $route['pattern']);
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
        
        if (empty($this->_routes)) {
            $this->_router = new StandartRouter();
            return $this->route();
        }
        
        return $this->route($this->getRouterType($request));
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
     * Returns routes by name of comparable
     * 
     * @param  string $comparableName Name of comparable
     * @return array
     */
    private function getRoutesByComparableName($comparableName)
    {
        $routes = array();
        
        foreach ($this->_routes as $name => $route) {
            if (isset($route[(string) $comparableName]))
                $routes[$name] = $route;
        }
        
        return $routes;
    }
    
    /**
     * Returns router type
     * 
     * @param  object \wilson\Request
     * @return array
     */
    private function getRouterType(Request $request)
    {
        foreach ($this->_routes as $name => $route) {
            if (isset($route['type'])) {
                switch ($route['type']) {
                    case 'static':
                        $routes = $this->getRoutesByComparableName('url');
                        $this->_router = new StaticRouter();
                        if ($this->_router->match($this->_uri, $routes))
                            return $routes;
                    case 'regex':
                        $routes = $this->getRoutesByComparableName('pattern');
                        $this->_router = new RegexRouter();
                        if ($this->_router->match($this->_uri, $routes))
                            return $routes;
                    default:
                        $this->_router = new StandartRouter();
                }
            }
        }
    }
    
    /**
     * Routing
     * 
     * @param  array $routes Routes
     * @return array
     */
    private function route(array $routes = null)
    {
        if ($this->_router instanceof RouterInterface)
            return $this->_router->route($this->_router->getActiveRouteName($this->_uri, $routes), $this->_routes);
        else
            return $this->_router->route($this->_uri);
    }
}
