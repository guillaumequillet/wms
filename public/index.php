<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\Front\ArticleController;

$controller = "App\\Controller\\Front\\" . ucfirst($_GET['controller']) . 'Controller';
$action = $_GET['action'];
$param = isset($_GET['param']) ? (int) $_GET['param'] : null;

(new $controller())->$action($param);