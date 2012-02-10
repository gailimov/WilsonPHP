<?php

// Change the following paths if necessary
$frontController = __DIR__ . '/../src/system/wilson/FrontController.php';
$config = __DIR__ . '/../src/app/config/test.php';

require_once $frontController;

use wilson\FrontController;

FrontController::getInstance()->init($config);
