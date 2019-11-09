<?php
declare(strict_types=1);

namespace App\Controller\Front;

use App\Model\Entity\Article;
use App\Model\Manager\ArticleManager;
use App\Tool\Token;

use League\Csv\Reader;

class ArticleController extends \App\Controller\Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->entityManager = new ArticleManager();
        $this->token = new Token();
    }

    public function delete(int $id): void
    {
        $this->entityManager->deleteArticle($id);
        header('location: index.php?page=article&action=showlist');
    }

    public function import(): void
    {
        if ($this->token->check() === false) {
            header('location: index.php?controller=article&action=showlist&param=0');
            exit();
        }

        if (array_key_exists("articleFile", $_FILES)) {
            $filename = $_FILES["articleFile"]["tmp_name"];
        }

        if (!isset($filename) || $filename === "") {
            header('location: index.php?controller=article&action=showlist&param=0');
        }

        $articles = [];

        $csv = Reader::CreateFromPath($filename, 'r');
        $csv->setHeaderOffset(0);
        foreach ($csv->getRecords() as $line) {
            $article = new Article();
            $data = [
                'code' => $line['code'],
                'description' => $line['description'],
                'weight' => (int) $line['weight'],
                'width' => (int) $line['width'],
                'length' => (int) $line['length'],
                'height' => (int) $line['height'],
                'barcode' => $line['barcode']
            ];

            $article->hydrate($data);
            $articles[] = $article;
        }

        $this->entityManager->createArticles($articles);
        header('location: index.php?controller=article&action=viewlist&param=1');
    }

    public function showlist(?int $param): void 
    {
        $template = 'article/list.twig.html';
        $articles = [];
        $res = $this->entityManager->findAllArticles();

        if (!is_null($res)) {
            foreach($res as $article) {
                $articles[] = (new Article())->hydrate($article);                
            }
        }

        $data = [
            'articles' => $articles,
        ];        

        if (!is_null($param)) {
            $data['param'] = $param;
        }

        if (!is_null($template) && !is_null($data)) {
            $data['token'] = $this->token->generateString();
            $this->getView()->render($template, $data);
        }
    }

    public function show(?int $id): void
    {
        $template = 'article/single.twig.html';
        $data = [];

        // if some article id was specified, we only show this one
        if (!is_null($id))
        {
            $res = $this->entityManager->findArticleWithId($id);
            $article = null;

            if (!is_null($res)) {
                $article = (new Article())->hydrate($res);
            }

            $data = [
                'article' => $article
            ];
        }

        if (!is_null($template) && !is_null($data)) {
            $data['token'] = $this->token->generateString();
            $this->getView()->render($template, $data);
        }
    }
}
