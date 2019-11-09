<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Article;

class ArticleRepository extends Repository
{
    public function deleteArticle(int $id): void
    {
        $req = $this->database->getPDO()->prepare('DELETE FROM articles WHERE id=:id');
        $req->execute(['id' => $id]);
    }

    public function createArticles(array $articles): void
    {
        foreach($articles as $article) {
            $this->createArticle($article);
        }
    }

    public function createArticle(Article $article): void
    {
        $req = $this->database->getPDO()->prepare('INSERT INTO articles(code, description, weight, width, length, height, barcode) 
            VALUES(:code, :description, :weight, :width, :length, :height, :barcode)');
        $req->execute([
            'code' => $article->getCode(),
            'description' => $article->getDescription(),
            'weight' => $article->getWeight(),
            'width' => $article->getWidth(),
            'length' => $article->getLength(),
            'height' => $article->getHeight(),
            'barcode' => $article->getBarcode()
        ]);
    }

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
