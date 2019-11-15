<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Article;

class ArticleRepository extends Repository
{
    public function deleteArticle(int $id): bool
    {
        $req = $this->database->getPDO()->prepare('DELETE FROM articles WHERE id=:id');
        $res = $req->execute(['id' => $id]);
        return $res;
    }

    public function createArticles(array $articles): void
    {
        foreach($articles as $article) {
            $this->createArticle($article);
        }
    }

    public function createArticle(Article $article): bool
    {
        $req = $this->database->getPDO()->prepare('INSERT INTO articles(code, description, weight, width, length, height, barcode) 
            VALUES(:code, :description, :weight, :width, :length, :height, :barcode)');
        $res = $req->execute([
            'code' => $article->getCode(),
            'description' => $article->getDescription(),
            'weight' => $article->getWeight(),
            'width' => $article->getWidth(),
            'length' => $article->getLength(),
            'height' => $article->getHeight(),
            'barcode' => $article->getBarcode()
        ]);
        return $res;
    }

    public function updateArticle(Article $article): bool
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
        return $res;
    }

    public function findArticlesCount(): int
    {
        $req = 'SELECT COUNT(*) as total FROM articles';
        $res = $this->database->getPDO()->query($req);
        return $res->fetch()['total'];
    }

    public function findAllArticles(?int $page = null, ?int $perPage = null): ?array
    {
        $req = 'SELECT * FROM articles';
        $fields = [];

        if (!is_null($page) && !is_null($perPage)) {
            $req .= ' LIMIT :limit OFFSET :offset';
            $fields['limit'] = $perPage;
            $fields['offset'] = $perPage * ($page - 1);
        }

        $stmt = $this->database->getPDO()->prepare($req);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'App\\Model\\Entity\\Article'); 
        $stmt->execute($fields);
        $res = $stmt->fetchAll();
        return ($res === false) ? null : $res;
    }

    public function findArticlesWithCodeLike(string $queryString, ?int $limit = null): ?array
    {
        $req = 'SELECT * FROM articles WHERE code LIKE :pattern';
        $fields = ['pattern' => '%' . $queryString . '%'];

        if (isset($limit)) {
            $req .= ' LIMIT :limit';
            $fields['limit'] = $limit;
        }

        $stmt = $this->database->getPDO()->prepare($req);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'App\\Model\\Entity\\Article'); 
        $stmt->execute($fields);
        $res = $stmt->fetchAll();
        return ($res === false) ? null : $res;
    }

    public function findArticleWithId(int $id): ?Article 
    {
        $stmt = $this->database->getPDO()->prepare('SELECT * FROM articles WHERE id=:id');
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'App\\Model\\Entity\\Article'); 
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch();
        return ($res === false) ? null : $res;
    }

    public function findArticleWithCode(string $code): ?Article 
    {
        $stmt = $this->database->getPDO()->prepare('SELECT * FROM articles WHERE code=:code');
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'App\\Model\\Entity\\Article'); 
        $stmt->execute(['code' => $code]);
        $res = $stmt->fetch();
        return ($res === false) ? null : $res;
    }
}
