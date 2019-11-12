<?php
declare(strict_types=1);

namespace App\Controller\Front;

use App\Model\Entity\Article;
use App\Model\Manager\ArticleManager;

use League\Csv\Reader;

class ArticleController extends \App\Controller\Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->entityManager = new ArticleManager();
    }

    public function update(int $id): void
    {
        if (!$this->token->check()) {
            header('location: /article/show/' . $id . '/0');
            exit();
        }

        $mandatoryKeys = [
            'code',
            'description',
            'weight',
            'width',
            'height',
            'length',
            'barcode'
        ];

        $allKeysOk = true;
        $data = [];

        foreach($mandatoryKeys as $key) {
            $data[$key] = html_entity_decode($this->superglobalManager->findVariable('post', $key));
            if (is_null($data[$key])) {
                $allKeysOk = false;
                break;
            }
        }            

        if (!$allKeysOk) {
            header('location: /article/show/' . $id . '/0');
            exit();
        }

        $data['weight'] = (int)$data['weight'];
        $data['width'] = (int)$data['width'];
        $data['height'] = (int)$data['height'];
        $data['length'] = (int)$data['length'];
        $article = new Article();
        $article->hydrate($data);

        $this->entityManager->updateArticle($article);
        header('location: /article/show/' . $id . '/1');
    }

    public function delete(int $id): void
    {
        $this->entityManager->deleteArticle($id);
        header('location: /article/showlist');
    }

    public function import(): void
    {
        if ($this->token->check() === false) {
            header('location: /article/showlist/0');
            exit();
        }

        if (array_key_exists("articleFile", $_FILES)) {
            $filename = $_FILES["articleFile"]["tmp_name"];
        }

        if (!isset($filename) || $filename === "") {
            header('location: /article/showlist/0');
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
        header('location: /article/showlist/1');
    }

    public function showlist(?int $param = null): void 
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
            'token' => $this->token->generateString()
        ];       

        if (!is_null($param)) {
            $data['param'] = $param;
        }

        $this->getView()->render($template, $data);
    }

    public function show(int $id, ?int $param = null): void
    {
        $template = 'article/single.twig.html';
        $data = [];

        // if some article id was specified, we only show this one
        if (!is_null($id))
        {
            $res = $this->entityManager->findArticleWithId($id);
            $article = null;
        }

        if (!is_null($res)) {
            $article = (new Article())->hydrate($res);
        }

        $data = [
            'article' => $article,
            'token' => $this->token->generateString()
        ];

        if (!is_null($param)) {
            $data['param'] = $param;
        }

        $this->getView()->render($template, $data);
    }
}
