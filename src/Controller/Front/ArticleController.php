<?php
declare(strict_types=1);

namespace App\Controller\Front;

use App\Model\Manager\ArticleManager;

use League\Csv\Reader;

class ArticleController extends \App\Controller\Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->entityManager = new ArticleManager();
    }

    public function create(): void 
    {
        $template = 'article/new.twig.html';
        $this->render($template);
    }

    public function update(int $id): void
    {
        $result = $this->entityManager->updateArticle();
        
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
        $this->entityManager->deleteArticle($id);
        header('location: /article/showlist');
    }

    public function import(): void
    {
        if ($this->token->check() === false) {
            $this->setLog('0');
            header('location: /article/showlist');
            exit();
        }

        if (array_key_exists("articleFile", $_FILES)) {
            $filename = $_FILES["articleFile"]["tmp_name"];
        }

        if (!isset($filename) || $filename === "") {
            $this->setLog('0');
            header('location: /article/showlist');
            exit();
        }

        $articles = [];
        $csv = Reader::CreateFromPath($filename, 'r');

        foreach ($csv->getRecords() as $k => $line) {
            if ($k !== 0) {
                $article = new Article();
                $data = [
                    'code' => $line[0],
                    'description' => $line[1],
                    'weight' => (int) $line[2],
                    'width' => (int) $line[3],
                    'length' => (int) $line[4],
                    'height' => (int) $line[5],
                    'barcode' => $line[6]
                ];

                $article->hydrate($data);
                $articles[] = $article;
            }
        }
        $this->entityManager->createArticles($articles);
        $this->setLog('1');
        header('location: /article/showlist');
    }

    public function showlist(): void 
    {
        $template = 'article/list.twig.html';
        $data = ['token' => $this->token->generateString()];

        // we check if some search was submitted
        $queryString = $this->superglobalManager->findVariable('post', 'queryString');
        $articles = $this->entityManager->findAllArticles($queryString);

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
            $article = $this->entityManager->findArticleWithId($id);
        }

        if (!is_null($article)) {
            $data['article'] = $article;
        }

        $this->render($template, $data);
    }
}
