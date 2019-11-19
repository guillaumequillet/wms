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
            echo "false";
        }

        echo ($this->manager->articleExists($code)) ? "true" : "false";
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
        header('location: /article/showlist');
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

    public function showlist(int $page = 1, ?string $queryString = null): void 
    {
        $template = 'article/list.twig.html';
        $data = ['token' => $this->token->generateString()];

        // if the queryString was not found in GET, we check in POST
        if (is_null($queryString)) {
            $queryString = $this->superglobalManager->findVariable('post', 'queryString');
        }

        if ($queryString === '') {
            $queryString = null;
        }

        $pageSize = 5;
        $articles = $this->manager->findAllArticles($queryString, $page, $pageSize);

        // pagination
        $articlesCount = $this->manager->getArticlesCount($queryString);

        if ($articlesCount > 0) {
            $data['currentPage'] = $page;
            $data['pageSize'] = $pageSize;
        }

        if (isset($data['currentPage']) && $page > 1) {
            $data['previousPage'] = $page - 1;
        }

        if (isset($data['currentPage']) && $page * $pageSize < $articlesCount) {
            $data['nextPage'] = $page + 1;
        }

        if (!is_null($articles)) {
            $data['articles'] = $articles;
        }

        if (!is_null($queryString)) {
            $data['queryString'] = $queryString;
        }

        $this->render($template, $data);
    }

    public function show(int $id): void
    {
        $template = 'article/single.twig.html';
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
