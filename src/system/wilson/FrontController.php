<?php

/**
 * WilsonPHP
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License v3
 */

namespace wilson;

use Symfony\Component\ClassLoader\UniversalClassLoader,
    wilson\router\Router,
    wilson\router\type\StaticRouter,
    wilson\router\type\RegexRouter,
    wilson\router\type\SegmentRouter;

/**
 * Front Controller
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class FrontController
{
    /**
     * Default action
     */
    const DEFAULT_ACTION = 'index';
    
    /**
     * Action postfix
     */
    const ACTION_POSTFIX = 'Action';
    
    /**
     * Config
     * 
     * @var array
     */
    public $config = array();
    
    /**
     * Singleton instance
     * 
     * @var \wilson\FrontController
     */
    private static $_instance;
    
    /**
     * Namespace
     * 
     * @var string
     */
    private $_namespace = 'app';
    
    /**
     * Modules directory
     * 
     * @var string
     */
    private $_modulesDir = 'modules';
    
    /**
     * Default module
     * 
     * @var string
     */
    private $_defaultModule = 'main';
    
    /**
     * Controllers directory
     * 
     * @var string
     */
    private $_controllersDir = 'controllers';
    
    /**
     * Default controller
     * 
     * @var string
     */
    private $_defaultController = 'site';
    
    /**
     * Module ID
     * 
     * @var string
     */
    private $_moduleId;
    
    /**
     * Directory ID
     * 
     * @var string
     */
    private $_directoryId;
    
    /**
     * Controller ID
     * 
     * @var string
     */
    private $_controllerId;
    
    /**
     * Action ID
     * 
     * @var string
     */
    private $_actionId;
    
    /**
     * Params
     * 
     * @var array
     */
    private $_params = array();
    
    /**
     * @var \wilson\router\Router
     */
    private $_router;
    
    private function __construct()
    {
    }
    
    private function __clone()
    {
    }
    
    /**
     * Returns singleton instance
     * 
     * @return \wilson\FrontController
     */
    public static function getInstance()
    {
        if (!self::$_instance)
            self::$_instance = new self();
        return self::$_instance;
    }
    
    /**
     * Initialization
     * 
     * @param  string $config Config file
     * @return \wilson\FrontController
     */
    public function init($config)
    {
        $this->initConfig($config)
             ->setNamespace($this->getConfigParam('namespace', $this->_namespace))
             ->initLoader()
             ->setOptions(array(
                 'modulesDir' => $this->getConfigParam('modulesDir', $this->_modulesDir),
                 'defaultModule' => $this->getConfigParam('defaultModule', $this->_defaultModule),
                 'controllersDir' => $this->getConfigParam('controllersDir', $this->_controllersDir),
                 'defaultController' => $this->getConfigParam('defaultController', $this->_defaultController)
             ));
        
        return $this;
    }
    
    /**
     * Running
     * 
     * @return void
     */
    public function run()
    {
        $this->dispatch(new Request);
    }
    
    /**
     * Sets namespace
     * 
     * @param  string $namespace Namespace
     * @return \wilson\FrontController
     */
    public function setNamespace($namespace)
    {
        $this->ensure(is_dir($this->config['basePath'] . '/../' . (string) $namespace),
                      'Namespace "' . (string) $namespace . '" not found');
        $this->_namespace = (string) $namespace;
        return $this;
    }
    
    /**
     * Sets modules directory
     * 
     * @param  string $dir Directory
     * @return \wilson\FrontController
     */
    public function setModulesDir($dir)
    {
        $this->ensure(is_dir($this->config['basePath'] . '/' . (string) $dir),
                      '"' . (string) $dir . '" is not a valid modules directory');
        $this->_modulesDir = (string) $dir;
        return $this;
    }
    
    /**
     * Returns modules directory
     * 
     * @return string
     */
    public function getModulesDir()
    {
        return $this->_modulesDir;
    }
    
    /**
     * Sets default module
     * 
     * @param  string $module Module
     * @return \wilson\FrontController
     */
    public function setDefaultModule($module)
    {
        $this->ensure(is_dir($this->config['basePath'] . '/' . $this->_modulesDir . '/' . (string) $module),
                      'Module "' . (string) $module . '" not found');
        $this->_defaultModule = (string) $module;
        return $this;
    }
    
    /**
     * Sets controllers directory
     * 
     * @param  string $dir Directory
     * @return \wilson\FrontController
     */
    public function setControllersDir($dir)
    {
        $this->ensure(
            is_dir(
                $this->config['basePath'] . '/' . $this->_modulesDir . '/' . $this->_defaultModule . '/' . (string) $dir
            ),
            '"' . (string) $dir . '" is not a valid controllers directory'
        );
        $this->_controllersDir = (string) $dir;
        return $this;
    }
    
    /**
     * Sets default controller
     * 
     * @param  string $controller Controller
     * @return \wilson\FrontController
     */
    public function setDefaultController($controller)
    {
        $this->ensure(
            class_exists(
                '\\' . $this->_namespace .
                '\\' . $this->_modulesDir .
                '\\' . $this->_defaultModule .
                '\\' . $this->_controllersDir .
                '\\' . ucfirst((string) $controller)
            ),
            'Controller class "' . ucfirst((string) $controller) . '" not found'
        );
        $this->_defaultController = (string) $controller;
        return $this;
    }
    
    /**
     * Sets module ID
     * 
     * @param  string $moduleId Module ID
     * @return \wilson\FrontController
     */
    public function setModuleId($moduleId)
    {
        $this->_moduleId = mb_strtolower((string) $moduleId, 'UTF-8');
        return $this;
    }
    
    /**
     * Returns module ID
     * 
     * @return string
     */
    public function getModuleId()
    {
        return $this->_moduleId;
    }
    
    /**
     * Sets directory ID
     * 
     * @param  string $directoryId Directory ID
     * @return \wilson\FrontController
     */
    public function setDirectoryId($directoryId)
    {
        $this->_directoryId = mb_strtolower((string) $directoryId, 'UTF-8');
        return $this;
    }
    
    /**
     * Returns directory ID
     * 
     * @return string
     */
    public function getDirectoryId()
    {
        return $this->_directoryId;
    }
    
    /**
     * Sets controller ID
     * 
     * @param  string $controllerId Controller ID
     * @return \wilson\FrontController
     */
    public function setControllerId($controllerId)
    {
        $this->_controllerId = mb_strtolower((string) $controllerId, 'UTF-8');
        return $this;
    }
    
    /**
     * Returns controller ID
     * 
     * @return string
     */
    public function getControllerId()
    {
        return $this->_controllerId;
    }
    
    /**
     * Sets action ID
     * 
     * @param  string $actionId Action ID
     * @return \wilson\FrontController
     */
    public function setActionId($actionId)
    {
        $this->_actionId = mb_strtolower((string) $actionId, 'UTF-8');
        return $this;
    }
    
    /**
     * Returns action ID
     * 
     * @return string
     */
    public function getActionId()
    {
        return $this->_actionId;
    }
    
    /**
     * Returns param value by key
     * 
     * @param  string $key     Key
     * @param  string $default Default value if key not exists
     * @return mixed || null
     */
    public function getParam($key, $default = null)
    {
        if (isset($this->_params[(string) $key]))
            return $this->_params[(string) $key];
        return $default;
    }
    
    /**
     * Sets params
     * 
     * @param  array $params Params
     * @return \wilson\FrontController
     */
    public function setParams(array $params)
    {
        $this->_params = $params;
        return $this;
    }
    
    /**
     * Returns params
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }
    
    /**
     * Returns router object
     * 
     * @return \wilson\router\Router
     */
    public function getRouter()
    {
        if (!$this->_router)
            $this->_router = new Router();
        return $this->_router;
    }
    
    /**
     * Initializes config
     * 
     * @param  string $config Config file
     * @return \wilson\FrontController
     */
    private function initConfig($config)
    {
        require_once 'Exception.php';
        $this->ensure(is_file($config), '"' . $config . '" is not a valid config file');
        $this->config = require_once $config;
        
        return $this;
    }
    
    /**
     * Initializes loader
     * 
     * @return \wilson\FrontController
     */
    private function initLoader()
    {
        require_once __DIR__ . '/../vendors/Symfony/Component/ClassLoader/UniversalClassLoader.php';
        
        $loader = new UniversalClassLoader();
        $loader->registerNamespaces(array(
            'wilson' => __DIR__ . '/..',
            $this->_namespace => $this->config['basePath'] . '/..',
            'Zend' => __DIR__ . '/../vendors'
        ));
        $loader->register();
        
        return $this;
    }
    
    /**
     * Dispatching
     * 
     * @param  object \wilson\Request
     * @return void
     */
    private function dispatch(Request $request)
    {
        if (isset($this->config['routes'])) {
            $this->getRouter()
                ->addTypes(array(
                    new StaticRouter(),
                    new RegexRouter(),
                    new SegmentRouter()
                ))
                ->addRoutes($this->config['routes']);
        }
        
        $options = $this->ensureRouteOptions($this->getRouter()->run($request));
        
        $this->ensure(file_exists($this->config['basePath'] . '/' . $this->_modulesDir . '/' . $options['module']),
                      'Module "' . $options['module'] . '" not found');
        
        $this->setRouteOptions($options);
        
        $directory = $options['directory'] ? '\\' . $options['directory'] : $options['directory'];
        
        $class = '\\' . $this->_namespace .
                 '\\' . $this->_modulesDir .
                 '\\' . $options['module'] .
                 '\\' . $this->_controllersDir .
                        $directory .
                 '\\' . ucfirst($options['controller']);
        
        $this->ensure(class_exists($class), 'Controller class "' . $class . '" not found');
        
        $obj = new $class($request);
        $action = $options['action'] . self::ACTION_POSTFIX;
        
        $this->ensure(is_callable(array($obj, $action)), 'Action "' . $class . '::' . $action . '()" not found');
        
        call_user_func(array($obj, $action), $options['params']);
    }
    
    /**
     * Sets default values if options params not exists
     * 
     * @param  array $options Options
     * @return array
     */
    private function ensureRouteOptions(array $options)
    {
        if (empty($options['module']))
            $options['module'] = $this->_defaultModule;
        if (empty($options['directory']))
            $options['directory'] = null;
        if (empty($options['controller']))
            $options['controller'] = $this->_defaultController;
        if (empty($options['action']))
            $options['action'] = self::DEFAULT_ACTION;
        if (empty($options['params']))
            $options['params'] = array();
        
        return $options;
    }
    
    /**
     * Sets route options
     * 
     * @param  array $options Options
     * @return void
     */
    private function setRouteOptions(array $options)
    {
        $this->setOptions(array(
            'moduleId' => $options['module'],
            'directoryId' => $options['directory'],
            'controllerId' => $options['controller'],
            'actionId' => $options['action'],
            'params' => $options['params']
        ));
    }
    
    /**
     * Returns config param by key
     * 
     * @param  string $key     Key
     * @param  mixed  $default Default value if key not exists
     * @return mixed || null
     */
    private function getConfigParam($key, $default = null)
    {
        if (isset($this->config[(string) $key]))
            return $this->config[(string) $key];
        return $default;
    }
    
    /**
     * Sets options
     * 
     * @param  array $options Options
     * @return \wilson\FrontController
     */
    private function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $setter = 'set' . ucfirst($key);
            $this->ensure(in_array($setter, get_class_methods(__CLASS__)), 'Property "' . $key . '" not exists');
            $this->$setter($value);
        }
        
        return $this;
    }
    
    /**
     * Throws an exception if the expression is false
     * 
     * @param  mixed  $expr    Expression
     * @param  string $message Error message
     * @return void
     */
    private function ensure($expr, $message)
    {
        if (!$expr)
            throw new Exception($message);
    }
}
