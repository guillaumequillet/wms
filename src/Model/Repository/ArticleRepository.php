<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Article;

class ArticleRepository extends Repository
{
    public function createArticle(Article $article): bool
    {
        $req = $this->database->getPDO()->prepare('INSERT INTO articles(code, description, weight, width, length, height, barcode) 
            VALUES(:code, :description, :weight, :width, :length, :height, :barcode)');
        return $req->execute([
            'code' => $article->getCode(),
            'description' => $article->getDescription(),
            'weight' => $article->getWeight(),
            'width' => $article->getWidth(),
            'length' => $article->getLength(),
            'height' => $article->getHeight(),
            'barcode' => $article->getBarcode()
        ]);
    }

    public function createArticles(array $articles): int
    {
        $reqString = 'INSERT INTO articles(code, description, weight, width, height, length, barcode) VALUES ';
        $reqValues = [];
        foreach ($articles as $article) {
            $reqValues[] = '("' . join('","', [$article->getCode(), $article->getDescription(), $article->getWeight(), $article->getWidth(), $article->getHeight(), $article->getLength(), $article->getBarcode()]) . '")';
        }
        $reqString .= join(',', $reqValues);
        $reqString .=  'ON DUPLICATE KEY UPDATE code=code';
        $res = $this->database->getPDO()->exec($reqString);
        return ($res === false) ? 0 : $res;
    }

    public function updateArticle(Article $article): bool
    {
        $req = $this->database->getPDO()->prepare('UPDATE articles 
            SET code=:code, description=:description, weight=:weight, width=:width, length=:length, height=:height, barcode=:barcode 
            WHERE code=:querycode');
        return $req->execute([
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

    public function findArticleHistory(Article $article): ?array
    {
        $this->database->getPDO()->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $query = 'SELECT articles.code AS code, locations.concatenate AS location, rows.type AS type, rows.movement as id, rows.qty AS qty, incomings.reference AS incRef, outgoings.reference AS outRef, incomings.created_at AS incDate, outgoings.created_at AS outDate'; 
        $query .= ' FROM `rows`';
        $query .= ' INNER JOIN locations ON locations.id = rows.location';    
        $query .= ' INNER JOIN articles ON articles.id = rows.article';
        $query .= ' LEFT JOIN incomings ON (incomings.id = rows.movement AND rows.type="incoming")';
        $query .= ' LEFT JOIN outgoings ON (outgoings.id = rows.movement AND rows.type="outgoing")';
        $query .= ' WHERE rows.article = :article';
        $query .= ' ORDER BY rows.id';

        $req = $this->database->getPDO()->prepare($query);
        $res = $req->execute(['article' => $article->getId()]);

        if ($res === null) {
            return null;
        }

        $res = $req->fetchAll();
        return empty($res) ? null : $res;
    }
}
