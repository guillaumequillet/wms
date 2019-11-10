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

        if (!is_null($getController)) {
            $class = "App\\Controller\\Front\\" . ucfirst($getController) . "Controller";
        }

        if (isset($class) && class_exists($class) && method_exists($class, $getAction)) {
            (new $class())->$getAction($param);
            exit();
        }

        // default
        (new ArticleController())->showlist();
    }
}
