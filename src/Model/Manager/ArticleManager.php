<?php
declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entity\Article;
use App\Model\Repository\ArticleRepository;
use League\Csv\Reader;

class ArticleManager extends Manager
{
    public function __construct() 
    {
        parent::__construct();
        $this->repository = new ArticleRepository();
    }

    public function deleteArticle(int $id): bool
    {
        return $this->repository->deleteWhere(['id', '=', $id]);
    }

    public function createArticle(): ?int
    {
        $article = new Article();
        $data = [
            'code' => $this->superglobalManager->findVariable("post", "code"),
            'description' => $this->superglobalManager->findVariable("post", "description"),
            'weight' => (int)$this->superglobalManager->findVariable("post", "weight"),
            'width' => (int)$this->superglobalManager->findVariable("post", "width"),
            'height' => (int)$this->superglobalManager->findVariable("post", "height"),
            'length' => (int)$this->superglobalManager->findVariable("post", "length"),
            'barcode' => $this->superglobalManager->findVariable("post", "barcode"),
        ];
        $article->hydrate($data);
        $res = $this->repository->createArticle($article);

        // Article was not created
        if (!$res) {
            return null;
        }

        $id = $this->repository->findWhere(['code', '=', $article->getCode()])->getId();
        return (is_null($id) ? null : $id);
    }

    public function articleExists(string $code): bool
    {
        return !(is_null($this->repository->findWhere(['code', '=', $code])));
    }

    public function createArticles(): bool
    {
        if ($this->superglobalManager->findFile('articleFile')) {
            $filename = $this->superglobalManager->findFile('articleFile')["tmp_name"];
        }

        if (!isset($filename) || $filename === "") {
            return false;
        }

        $articles = [];
        $csv = Reader::CreateFromPath($filename, 'r');

        foreach ($csv->getRecords() as $k => $line) {
            if ($k !== 0) {
                # exit if header is incorrect
                if (sizeof($line) < 7) {
                    return false;
                }
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

        foreach ($articles as $article) {
            $articleExists = !(is_null($this->repository->findWhere(['code', '=', $article->getCode()])));
            
            if (!$articleExists) {
                $this->repository->createArticle($article);
            }

            if ($articleExists) {
                $this->repository->updateArticle($article);
            }            
        }
        return true;
    }

    public function updateArticle(): bool 
    {
        $data = [];

        $mandatoryKeys = [
            'code',
            'description',
            'weight',
            'width',
            'height',
            'length',
            'barcode'
        ];

        foreach($mandatoryKeys as $key) {
            $value = html_entity_decode($this->superglobalManager->findVariable('post', $key));

            if ($key === 'code' || $key === 'description' || $key === 'barcode') {
                $data[$key] = (string)$value;
            }

            if ($key === 'weight' || $key === 'width' || $key === 'height' || $key === 'length') {
                $data[$key] = (int)$value;
            }

            if (is_null($data[$key])) {
                return false;
            }
        }            
       
        $article = new Article();
        $article->hydrate($data);
        return $this->repository->updateArticle($article);
    }

    public function findAllArticles(?string $queryString = null, ?int $page = null, ?int $pageSize = null): ?array
    {
        return $this->repository->findWhereAllPaginated(['code', 'like', "%$queryString%"], $page, $pageSize);
    }

    public function findArticleWithId(int $id): ?Article
    {
        return $this->repository->findWhere(['id', '=', $id]);
    }
}
