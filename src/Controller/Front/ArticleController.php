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
        $data = ['token0' => $this->token->generateString(0)];
        $this->render($template, $data);
    }

    public function record(): void
    {
        if (!$this->token->check(0)) {
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
        header('Content-type: application/json');

        $code = $this->superglobalManager->findVariable('post', 'code');

        if (is_null($code)) {
            echo json_encode(false);
            exit();
        }

        $exists = $this->manager->articleExists($code);
        echo json_encode($exists);
    }

    public function suggestions(): void
    {
        header('Content-type: application/json');

        $code = $this->superglobalManager->findVariable('post', 'code');

        if (is_null($code)) {
            echo json_encode([]);
            exit();
        }

        $array = $this->manager->suggestArticles($code);
        echo json_encode($array);
    }

    public function update(int $id): void
    {
        $result = $this->manager->updateArticle();
        
        if (!$result || !$this->token->check(0)) {
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
        $this->setLog($res ? 'articleDeleteOK' : 'articleDeleteError');
        header('location: /article/index');
    }

    public function import(): void
    {
        if ($this->token->check(0) === false) {
            $this->setLog('noneInterval');
            header('location: /article/index');
            exit();
        }

        $res = $this->manager->createArticles();
        $this->setLog($res);
        header('location: /article/index');
    }

    public function index(int $page = 0): void 
    {
        // we reset the queryString if new access from menu
        if ($page === 0) {
            $page = 1;
            $this->superglobalManager->unsetVariable('session', 'queryString');
        }

        $postQueryString = $this->superglobalManager->findVariable('post', 'queryString');

        if (!is_null($postQueryString) && $this->token->check(1)) {
            $this->superglobalManager->setVariable('session', 'queryString', $postQueryString);
        }

        $queryString = $this->superglobalManager->findVariable('session', 'queryString');

        if (!is_null($queryString) && $queryString === '') {
            $queryString = null;
        }

        $template = 'article/index.twig.html';
        $data = ['token0' => $this->token->generateString(0), 'token1' => $this->token->generateString(1)];

        if (!is_null($queryString)) {
            $data['queryString'] = $queryString;
        }

        $articles = $this->manager->findAllArticles($queryString, $page);

        foreach (['entities', 'currentPage', 'previousPage', 'nextPage'] as $key) {
            if (isset($articles[$key]) && !is_null($articles[$key])) {
                $data[$key] = $articles[$key];
            }
        }
        $this->render($template, $data);
    }

    public function show(int $id): void
    {
        $template = 'article/show.twig.html';
        $data = ['token0' => $this->token->generateString()];

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

    public function history(int $id): void
    {
        $template = 'article/history.twig.html';
        $data = [];
        $data['code'] = $this->manager->findArticleWithId($id)->getCode();
        $data['lines'] = $this->manager->findArticleHistory($id);
        $this->render($template, $data);
    }
}
