<?php

/**
 * WilsonPHP
 * 
 * @copyright Copyright (c) 2012 Kanat Gailimov (http://kanat.gailimov.kz)
 * @license   http://www.gnu.org/licenses/gpl.html GNU General Public License v3
 */

namespace wilson\router;

/**
 * Standart router
 * 
 * @author Kanat Gailimov <gailimov@gmail.com>
 */
class StandartRouter
{
    /**
     * Routing
     * 
     * @param  string $uri URI
     * @return array
     */
    public function route($uri)
    {
        $uri = explode('/', (string) $uri);
        
        if ($uri[0] == 'index.php')
            array_shift($uri);
        
        $module = array_shift($uri);
        $controller = array_shift($uri);
        $action = array_shift($uri);
        if (!empty($uri)) {
            if (count($uri) % 2 !== 0)
                throw new Exception('Params must be in key-value form');
            $keys = $values = array();
            for ($i = 0; $i < count($uri); $i++) {
                if ($i % 2 == 0)
                    $keys[] = $uri[$i];
                else
                    $values[] = $uri[$i];
            }
            $_GET = array_merge(array_combine($keys, $values), $_GET);
        }
        
        return array(
            'module' => $module,
            'controller' => $controller,
            'action' => $action,
            'params' => $_GET
        );
    }
}
