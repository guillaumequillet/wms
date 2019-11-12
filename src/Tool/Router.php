<?php
declare(strict_types=1);

namespace App\Tool;

use App\Controller\Front\ArticleController;

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

        $this->router->map('GET', '/article/showlist', function() {
            (new ArticleController)->showlist();
        });

        $this->router->map('GET', '/article/showlist/[i:id]', function($id) {
            (new ArticleController)->showlist((int)$id);
        });

        $this->router->map('GET', '/article/show/[i:id]', function($id) {
            (new ArticleController)->show((int)$id);
        });

        $this->router->map('GET', '/article/delete/[i:id]', function($id) {
            (new ArticleController)->delete((int)$id);
        });

        $this->router->map('POST', '/article/import', function() {
            (new ArticleController)->import();
        });

        $this->router->map('POST', '/article/update/[i:id]', function($id) {
            (new ArticleController)->update((int)$id);
        });

        $this->router->map('GET', '/article/show/[i:id]/[i:param]', function($id, $param) {
            (new ArticleController)->show((int)$id, (int)$param);
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
