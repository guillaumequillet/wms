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

    public function findAllArticles(): ?array
    {
        return $this->repository->findAllArticles();
    }

    public function findArticleWithId(int $id): ?array
    {
        return $this->repository->findArticleWithId($id);
    }
}
