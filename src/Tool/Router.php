<?php
declare(strict_types=1);

namespace App\Tool;

use App\Controller\Front\ArticleController;
use App\Controller\Front\MovementController;
use App\Controller\Front\LoginController;
use App\Controller\Back\UserController;
use App\Controller\Back\LocationController;
use App\Controller\Front\LocationController as FrontLocationController;

class Router
{
    private $router;
    private $loginController;

    public function __construct() 
    {
        $this->router = new \AltoRouter();
        $this->loginController = new LoginController();
        $this->createRoutes();
    }

    public function unfound(): void
    {
        if (!$this->loginController->connected()) {
            header('location: /login');
            exit();
        }
        header('location: /article/index');
    }

    public function createRoutes(): void 
    {
        // routes to LOGIN
        if (!$this->loginController->connected()) {
            $this->router->map('GET', '/login', function() {
                $this->loginController->login();
            });

            $this->router->map('POST', '/login', function() {
                $this->loginController->login();
            });
        }

        // all the ADMIN routes
        if ($this->loginController->isAdmin()) {
            $this->createUserRoutes();
            $this->createLocationRoutes();
        }

        // all other routes IF LOGGED
        if ($this->loginController->connected()) {
            $this->router->map('GET', '/', function() {
                $this->unfound();
            });

            $this->router->map('GET', '/logout', function() {
                $this->loginController->logout();
            });
    
            $this->createArticleRoutes();
            $this->createMovementRoutes();
            $this->createFrontLocationRoutes();
        }
    }

    private function createArticleRoutes(): void 
    {
        $this->router->map('GET', '/article/index/[i:page]?', function(int $page = 0) {
            (new ArticleController)->index($page);
        });

        // we also need some route from POST, for the search input form
        $this->router->map('POST', '/article/index/[i:page]?', function(int $page = 0) {
            (new ArticleController)->index($page);
        });
    
        $this->router->map('GET', '/article/new', function() {
            (new ArticleController)->create();
        });

        $this->router->map('POST', '/article/record', function() {
            (new ArticleController)->record();
        });

        $this->router->map('GET', '/article/show/[i:id]', function(int $id) {
            (new ArticleController)->show((int)$id);
        });

        $this->router->map('GET', '/article/delete/[i:id]', function(int $id) {
            (new ArticleController)->delete((int)$id);
        });

        $this->router->map('POST', '/article/import', function() {
            (new ArticleController)->import();
        });

        $this->router->map('POST', '/article/update/[i:id]', function(int $id) {
            (new ArticleController)->update((int)$id);
        });

        $this->router->map('POST', '/article/exists', function() {
            (new ArticleController)->articleExists();
        });

        $this->router->map('POST', '/article/suggestions', function() {
            (new ArticleController)->suggestions();
        });
    }

    private function createMovementRoutes(): void 
    {
        $this->router->map('GET', '/movement/index', function() {
            (new MovementController)->index();
        });

        $this->router->map('GET', '/movement/incoming', function() {
            (new MovementController)->incoming();
        });

        $this->router->map('POST', '/movement/incoming', function() {
            (new MovementController)->incoming();
        });
    }

    private function createUserRoutes(): void
    {
        $this->router->map('GET', '/user/index', function() {
            (new UserController)->index();
        });        

        $this->router->map('POST', '/user/create', function() {
            (new UserController)->create();
        });

        $this->router->map('GET', '/user/delete/[i:id]', function(int $id) {
            (new UserController)->delete($id);
        });

        $this->router->map('GET', '/user/show/[i:id]', function(int $id) {
            (new UserController)->show($id);
        });

        $this->router->map('POST', '/user/update/[i:id]', function(int $id) {
            (new UserController)->update($id);
        });
    }

    private function createLocationRoutes(): void
    {
        $this->router->map('GET', '/location/index/[i:page]?', function(int $page = 0) {
            (new LocationController)->index($page);
        });        

        // we also need some route from POST, for the search input form
        $this->router->map('POST', '/location/index/[i:page]?', function(int $page = 0) {
            (new LocationController)->index($page);
        });

        $this->router->map('POST', '/location/createsingle', function() {
            (new LocationController)->createSingle();
        });

        $this->router->map('POST', '/location/createinterval', function() {
            (new LocationController)->createInterval();
        });

        $this->router->map('POST', '/location/import', function() {
            (new LocationController)->import();
        });

        $this->router->map('GET', '/location/delete/[i:id]', function(int $id) {
            (new LocationController)->delete($id);
        });
    }

    private function createFrontLocationRoutes(): void
    {
        $this->router->map('POST', '/location/suggestions', function() {
            (new FrontLocationController)->suggestions();
        });        

        $this->router->map('POST', '/location/exists', function() {
            (new FrontLocationController)->locationExists();
        });  
    }

    public function getRoute(): void
    {
        $match = $this->router->match();

        if ($match !== false) {
            call_user_func_array($match['target'], $match['params']);
        }

        // if route is not correct
        if ($match === false) {
            $this->unfound();
        }
    }
}
