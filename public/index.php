<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

use App\Tool\Router;

(new Router())->getRoute();
