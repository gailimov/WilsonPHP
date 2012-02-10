<?php

/**
 * WilsonPHP
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License v3
 */

namespace wilson;

/**
 * Base controller
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class Controller
{
    /**
     * Views dir
     */
    const VIEWS_DIR = 'views';
    
    /**
     * Layouts dir
     */
    const LAYOUTS_DIR = 'layouts';
    
    /**
     * Default layout
     */
    const DEFAULT_LAYOUT = 'main';
    
    /**
     * Content key
     */
    const CONTENT_KEY = 'content';
    
    /**
     * @var \wilson\Request
     */
    private $_request;
    
    /**
     * Constructor
     * 
     * @param \wilson\Request
     */
    public function __construct(Request $request)
    {
        $this->_request = $request;
    }
    
    /**
     * Returns Request object
     * 
     * @return \wilson\Request
     */
    public function getRequest()
    {
        return $this->_request;
    }
    
    /**
     * Returns base URL
     * 
     * @see \wilson\Request::getBaseUrl()
     * 
     * @param  bool $absolute URL should be absolute?
     * @param  bool $https    Use HTTPS?
     * @return string
     */
    public function getBaseUrl($absolute = false, $https = false)
    {
        return $this->_request->getBaseUrl($absolute, $https);
    }
    
    /**
     * Returns module ID
     * 
     * @see \wilson\FrontController::getModuleId()
     * 
     * @return string
     */
    public function getModuleId()
    {
        return FrontController::getInstance()->getModuleId();
    }
    
    /**
     * Returns controller ID
     * 
     * @see \wilson\FrontController::getControllerId()
     * 
     * @return string
     */
    public function getControllerId()
    {
        return FrontController::getInstance()->getControllerId();
    }
    
    /**
     * Returns action ID
     * 
     * @see \wilson\FrontController::getActionId()
     * 
     * @return string
     */
    public function getActionId()
    {
        return FrontController::getInstance()->getActionId();
    }
    
    /**
     * Returns param value by key
     * 
     * @see \wilson\FrontController::getParam()
     * 
     * @param  string $key     Key
     * @param  string $default Default value if key not exists
     * @return mixed || null
     */
    public function getParam($key, $default = null)
    {
        return FrontController::getInstance()->getParam($key, $default);
    }
    
    /**
     * Returns params
     * 
     * @see \wilson\FrontController::getParams()
     * 
     * @return array
     */
    public function getParams()
    {
        return FrontController::getInstance()->getParams();
    }
    
    /**
     * Creates URL
     * 
     * @see \wilson\router\RegexRouter::createUrl()
     * 
     * @param  string $name     Route name
     * @param  array  $params   Params
     * @param  bool   $absolute URL should be absolute?
     * @param  bool   $https    Use HTTPS?
     * @return string
     */
    public function createUrl($name, array $params = null, $absolute = false, $https = false)
    {
        return FrontController::getInstance()->getRouter()->createUrl($name, $params, $absolute, $https);
    }
    
    /**
     * Renders template
     * 
     * @param  array $params Params
     * @return string
     */
    public function render($params = array())
    {
        echo $this->fetch($this->getControllerId() . '/' . $this->getActionId(), $params);
    }
    
    /**
     * Renders partial template
     * 
     * @param  array $params Params
     * @return string
     */
    public function renderPartial($params = array())
    {
        echo $this->fetchPartial($this->getControllerId() . '/' . $this->getActionId(), $params);
    }
    
    /**
     * Fetches template
     * 
     * @param  string $template Template
     * @param  array  $params   Params
     * @return string
     */
    private function fetch($template, $params = array())
    {
        $content = $this->fetchPartial($template, $params);
        return $this->fetchPartial(
            self::LAYOUTS_DIR . '/' . self::DEFAULT_LAYOUT,
            array(self::CONTENT_KEY => $content)
        );
    }
    
    /**
     * Fetches partial template
     * 
     * @param  string $template Template
     * @param  array  $params   Params
     * @return string
     */
    private function fetchPartial($template, $params = array())
    {
        extract($params, EXTR_SKIP);
        ob_start();
        include_once FrontController::getInstance()->config['basePath'] . '/' .
                     FrontController::getInstance()->getModulesDir() . '/' .
                     $this->getModuleId() . '/' . self::VIEWS_DIR . '/' . $template . '.php';
        
        return ob_get_clean();
    }
}
