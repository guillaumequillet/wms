<?php
declare(strict_types=1);

namespace App\Tool;

use App\Controller\Front\ArticleController;
use App\Controller\Back\UserController;

class Router
{
    private $router;

    public function __construct() 
    {
        $this->router = new \AltoRouter();
        $this->createRoutes();
    }

    public function unfound(): void
    {
        header('location: /article/showlist');
    }

    public function createRoutes(): void 
    {
        $this->router->map('GET', '/', function() {
            $this->unfound();
        });

        $this->createArticleRoutes();
        $this->createUserRoutes();
    }

    public function createArticleRoutes(): void 
    {
        $this->router->map('GET', '/article/showlist/[i:page]?', function(int $page = null) {
            (new ArticleController)->showlist($page);
        });

        // we also need some route from POST, for the search input form
        $this->router->map('POST', '/article/showlist/[i:page]?', function(int $page = null) {
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
    }

    public function createUserRoutes(): void
    {
        $this->router->map('GET', '/user/index', function() {
            (new UserController)->index();
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
