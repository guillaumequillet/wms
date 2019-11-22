<?php
declare(strict_types=1);

namespace App\Controller\Front;

use App\Model\Manager\ArticleManager;
use App\Controller\Controller;

class ArticleController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->manager = new ArticleManager();
    }

    public function create(): void 
    {
        $template = 'article/new.twig.html';
        $data = ['token' => $this->token->generateString()];
        $this->render($template, $data);
    }

    public function record(): void
    {
        if (!$this->token->check()) {
            $this->setLog('0');
            header('location: /article/new');
            exit();
        }

        $id = $this->manager->createArticle();

        if (is_null($id)) {
            $this->setLog('1');
            header('location: /article/new');            
            exit();
        }

        $this->setLog('3');
        header('location: /article/show/' . $id);
    }

    public function articleExists(): void
    {
        $code = $this->superglobalManager->findVariable('post', 'code');

        if (is_null($code)) {
            echo 'false';
            exit();
        }

        echo ($this->manager->articleExists($code)) ? 'true' : 'false';
    }

    public function suggestions(): void
    {
        $code = $this->superglobalManager->findVariable('post', 'code');

        if (is_null($code)) {
            echo 'no-result';
            exit();
        }

        echo $this->manager->suggestArticles($code);        
    }

    public function update(int $id): void
    {
        $result = $this->manager->updateArticle();
        
        if (!$result || !$this->token->check()) {
            $this->setLog('0');
            header('location: /article/show/' . $id);
            exit();
        }

        $this->setLog('1');
        header('location: /article/show/' . $id);
    }

    public function delete(int $id): void
    {
        $res = $this->manager->deleteArticle($id);
        if (!$res) {
            $this->setLog('2');
        }
        header('location: /article/index');
    }

    public function import(): void
    {
        if ($this->token->check() === false) {
            $this->setLog('0');
            header('location: /article/showlist');
            exit();
        }

        $res = $this->manager->createArticles();
        $this->setLog($res ? '1' : '0');
        header('location: /article/showlist');
    }

    public function index(int $page = 0): void 
    {
        // we reset the queryString if new access from menu
        if ($page === 0) {
            $page = 1;
            $this->superglobalManager->unsetVariable('session', 'queryString');
        }

        $postQueryString = $this->superglobalManager->findVariable('post', 'queryString');
        
        if (!is_null($postQueryString)) {
            $this->superglobalManager->setVariable('session', 'queryString', $postQueryString);
        }

        $queryString = $this->superglobalManager->findVariable('session', 'queryString');

        if (!is_null($queryString) && $queryString === '') {
            $queryString = null;
        }

        $template = 'article/index.twig.html';
        $data = ['token' => $this->token->generateString()];

        $articles = $this->manager->findAllArticles($queryString, $page);

        foreach (['entities', 'currentPage', 'previousPage', 'nextPage', 'queryString'] as $key) {
            if (isset($articles[$key]) && !is_null($articles[$key])) {
                $data[$key] = $articles[$key];
            }
        }
        $this->render($template, $data);
    }

    public function show(int $id): void
    {
        $template = 'article/show.twig.html';
        $data = ['token' => $this->token->generateString()];

        // if some article id was specified, we only show this one
        if (!is_null($id))
        {
            $article = $this->manager->findArticleWithId($id);
        }

        if (!is_null($article)) {
            $data['article'] = $article;
        }

        $this->render($template, $data);
    }
}
