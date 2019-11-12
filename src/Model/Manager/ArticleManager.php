<?php
declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entity\Article;
use App\Model\Repository\ArticleRepository;

class ArticleManager extends Manager
{
    public function __construct() 
    {
        $this->repository = new ArticleRepository();
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

    public function updateArticle(Article $article): void {
        $this->repository->updateArticle($article);
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
