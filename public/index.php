<?php
declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

session_start();

use App\Controller\Front\ArticleController;
use App\Tool\SuperglobalManager;

/* toDelete */
function myLog(string $message): void
{
    echo "<script>console.log(\"" . $message . "\");</script>";
}
/* /toDelete */

// Rooter
$superglobalManager = new SuperglobalManager();

$getController = $superglobalManager->findVariable("get", "controller");
$getAction = $superglobalManager->findVariable("get", "action");
$getParam = $superglobalManager->findVariable("get", "param");
$param = is_null($getParam) ? null : (int) $getParam;

if (is_null($getController)) {
    $getController = "article";
}
$controller = "App\\Controller\\Front\\" . ucfirst($getController) . "Controller";

if (is_null($getAction)) {
    $getAction = "show";
}
$action = $getAction;

if (!class_exists($controller) || !method_exists($controller, $getAction)) {
    $controller = "App\\Controller\\Front\\ArticleController";
    $action = "showlist";
}

(new $controller())->$action($param);
