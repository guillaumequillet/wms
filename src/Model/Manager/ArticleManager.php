<?php
declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Repository\ArticleRepository;

class ArticleManager extends Manager
{
    public function __construct() 
    {
        $this->repository = new ArticleRepository();
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
