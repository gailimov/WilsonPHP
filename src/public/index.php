<?php

error_reporting(E_ALL | E_STRICT);

// Change the following paths if necessary
$frontController = __DIR__ . '/../system/wilson/FrontController.php';
$config = __DIR__ . '/../app/config/main.php';

require_once $frontController;

use wilson\FrontController;

FrontController::getInstance()->init($config)->run();
