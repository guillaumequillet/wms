<?php
declare(strict_types=1);

namespace App\Tool;

use App\Controller\Front\ArticleController;
use App\Controller\Front\LoginController;
use App\Controller\Back\UserController;
use App\Controller\Back\LocationController;

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
        header('location: /article/showlist');
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
        }
    }

    private function createArticleRoutes(): void 
    {
        $this->router->map('GET', '/article/showlist/[i:page]?', function(int $page = 1) {
            (new ArticleController)->showlist($page);
        });

        // we also need some route from POST, for the search input form
        $this->router->map('POST', '/article/showlist/[i:page]?', function(int $page = 1) {
            (new ArticleController)->showlist($page);
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
    }

    private function createUserRoutes(): void
    {
        $this->router->map('GET', '/user/index', function() {
            (new UserController)->index();
        });        

        $this->router->map('POST', '/user/createsingle', function() {
            (new UserController)->createSingle();
        });

        $this->router->map('POST', '/user/update', function() {
            (new UserController)->updateSingle();
        });
    }

    private function createLocationRoutes(): void
    {
        $this->router->map('GET', '/location/index/[i:page]?', function(int $page = 1) {
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
