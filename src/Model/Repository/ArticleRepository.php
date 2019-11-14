<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Article;

class ArticleRepository extends Repository
{
    public function test(): Article 
    {
        $db = $this->database->getPDO();
        $stmt = $db->prepare("SELECT * FROM articles LIMIT 1 OFFSET 1");
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'App\\Model\\Entity\\Article'); 
        $stmt->execute();
        $obj = $stmt->fetch();
        return $obj;
    }

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

    public function updateArticle(Article $article): void
    {
        $req = $this->database->getPDO()->prepare('UPDATE articles 
            SET code=:code, description=:description, weight=:weight, width=:width, length=:length, height=:height, barcode=:barcode 
            WHERE code=:querycode');
        $res = $req->execute([
            'code' => $article->getCode(),
            'description' => $article->getDescription(),
            'weight' => $article->getWeight(),
            'width' => $article->getWidth(),
            'length' => $article->getLength(),
            'height' => $article->getHeight(),
            'barcode' => $article->getBarcode(),
            'querycode' => $article->getCode()
        ]);
    }

    public function findAllArticles(): ?array
    {
        $stmt = $this->database->getPDO()->prepare('SELECT * FROM articles');
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'App\\Model\\Entity\\Article'); 
        $stmt->execute();
        $res = $stmt->fetchAll();
        return ($res === false) ? null : $res;
    }

    public function findArticlesWithCodeLike(string $queryString): ?array
    {
        $stmt = $this->database->getPDO()->prepare('SELECT * FROM articles WHERE code LIKE :pattern');
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'App\\Model\\Entity\\Article'); 
        $stmt->execute(['pattern' => '%' . $queryString . '%']);
        $res = $stmt->fetchAll();
        return ($res === false) ? null : $res;
    }

    public function findArticleWithId(int $id): ?array 
    {
        $req = $this->database->getPDO()->prepare('SELECT * FROM articles WHERE id=:id');
        $req->execute(['id' => $id]);
        $res = $req->fetch();
        return ($res === false) ? null : $res;
    }

    public function findArticleWithCode(string $code): ?array 
    {
        $req = $this->database->getPDO()->prepare('SELECT * FROM articles WHERE code=:code');
        $req->execute(['code' => $code]);
        $res = $req->fetch();
        return ($res === false) ? null : $res;
    }
}
