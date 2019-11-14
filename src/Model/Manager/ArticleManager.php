<?php
declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entity\Article;
use App\Model\Repository\ArticleRepository;

class ArticleManager extends Manager
{
    public function __construct() 
    {
        parent::__construct();
        $this->repository = new ArticleRepository();
    }

    public function test(): Article
    {
        $result = $this->repository->test();
        return $result;
    }

    public function deleteArticle(int $id): void
    {
        $this->repository->deleteArticle($id);
    }

    public function createArticles(array $articles): void
    {
        foreach ($articles as $article) {
            $articleExists = !(is_null($this->repository->findArticleWithCode($article->getCode())));
            
            if (!$articleExists) {
                $this->repository->createArticle($article);
            }

            if ($articleExists) {
                $this->repository->updateArticle($article);
            }            
        }
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

    public function findAllArticles(?string $queryString = null): ?array
    {
        if (is_null($queryString)) {
            return $this->repository->findAllArticles($queryString);
        }

        if (!is_null($queryString)) {
            return $this->repository->findArticlesWithCodeLike($queryString);
        }
    }

    public function findArticleWithId(int $id): ?array
    {
        return $this->repository->findArticleWithId($id);
    }
}
