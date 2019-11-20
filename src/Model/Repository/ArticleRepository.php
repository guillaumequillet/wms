<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Article;

class ArticleRepository extends Repository
{
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

    public function findArticlesCount(?string $queryString = null): int
    {
        $reqString = 'SELECT COUNT(*) as total FROM articles';
        $params = [];

        if (!is_null($queryString)) {
            $reqString .= ' WHERE code LIKE :queryString';
            $params['queryString'] = '%' . $queryString . '%';
        }

        $req = $this->database->getPDO()->prepare($reqString);
        $res = $req->execute($params);

        return ($res === false) ? 0 : $req->fetch()['total'];
    }

    public function findAllArticles(?string $queryString = null, ?int $page = null, ?int $perPage = null): ?array
    {
        $req = 'SELECT * FROM articles';
        $fields = [];

        if (!is_null($queryString)) {
            $req .= ' WHERE code LIKE :pattern';
            $fields['pattern'] = $queryString;
        }

        if (!is_null($page) && !is_null($perPage)) {
            $req .= ' LIMIT :limit OFFSET :offset';
            $fields['limit'] = $perPage;
            $fields['offset'] = $perPage * ($page - 1);
        }

        $stmt = $this->database->getPDO()->prepare($req);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, Article::class); 
        $stmt->execute($fields);
        $res = $stmt->fetchAll();
        return ($res === false) ? null : $res;
    }
}
