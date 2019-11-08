<?php
declare(strict_types=1);

namespace App\Model\Repository;

class ArticleRepository extends Repository
{
    public function findAllArticles(): ?array
    {
        $req = $this->database->getPDO()->query('SELECT * FROM articles');
        return ($req === false) ? null : $req->fetchAll();
    }

    public function findArticleWithId(int $id): ?array 
    {
        $req = $this->database->getPDO()->prepare('SELECT * FROM articles WHERE id=:id');
        $req->execute(['id' => $id]);
        $res = $req->fetch();
        return ($res === false) ? null : $res;
    }
}
