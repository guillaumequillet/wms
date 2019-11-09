<?php
declare(strict_types=1);

namespace App\Tool;

use App\Tool\SuperglobalManager;
use App\Controller\Front\ArticleController;

class Rooter
{
    private $superglobalManager;

    public function __construct() 
    {
        $this->superglobalManager = new SuperglobalManager();
    }

    public function getRoute(): void
    {
        $getController = $this->superglobalManager->findVariable("get", "controller");
        $getAction = $this->superglobalManager->findVariable("get", "action");
        $getParam = $this->superglobalManager->findVariable("get", "param");
        $param = is_null($getParam) ? null : (int) $getParam;

        if (is_null($getController) || is_null($getAction))
        {
            $getController = "article";
            $getAction = "show";
            $param = null;
        }

        $controller = "App\\Controller\\Front\\" . ucfirst($getController) . "Controller";
        $action = $getAction;

        if (!class_exists($controller) || !method_exists($controller, $getAction)) {
            $controller = "App\\Controller\\Front\\ArticleController";
            $action = "showlist";
            $param = null;
        }

        (new $controller())->$action($param);
    }
}
