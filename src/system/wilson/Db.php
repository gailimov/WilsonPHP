<?php

/**
 * WilsonPHP
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License v3
 */

namespace wilson;

/**
 * Database class
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class Db
{
    /**
     * @var \Zend\Db\Adapter\PdoMysql
     */
    protected $_db;
    
    public function __construct()
    {
        $this->_db = new \Zend\Db\Adapter\PdoMysql(FrontController::getInstance()->config['db']);
    }
}
