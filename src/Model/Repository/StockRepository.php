<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Article;
use App\Model\Entity\Location;
use App\Model\Entity\Stock;
use App\Model\Entity\Row;

class StockRepository extends Repository
{
    public function createStocks(array $stocks): void
    {
        $reqString = 'INSERT INTO stocks(location, article, qty) VALUES ';
        $reqValues = [];
        foreach ($stocks as $stock) {
            $reqValues[] = '("' . join('","', [$stock->getLocation()->getId(), $stock->getArticle()->getId(), $stock->getQty()]) . '")';
        }
        $reqString .= join(',', $reqValues);
        $reqString .=  'ON DUPLICATE KEY UPDATE qty=qty+VALUES(qty)';
        $query = $this->database->getPDO()->exec($reqString);        
    }

    private function getArticleFromId(int $id): ?Article
    {
        $req = $this->database->getPDO()->prepare('SELECT * FROM articles WHERE id=:id LIMIT 1');
        $req->setFetchMode(\PDO::FETCH_CLASS, Article::class);
        $req->execute(['id' => $id]);
        $res = $req->fetch();
        return (empty($res)) ? null : $res;
    }

    private function getLocationFromId(int $id): ?Location
    {
        $req = $this->database->getPDO()->prepare('SELECT * FROM locations WHERE id=:id LIMIT 1');
        $req->setFetchMode(\PDO::FETCH_CLASS, Location::class);
        $req->execute(['id' => $id]);
        $res = $req->fetch();
        return (empty($res)) ? null : $res;
    }

    public function findAllStocks(?int $page): ?array
    {
        $stocks = $this->findWhereAll([], $limit = $this->recordsPerPage, $offset = ($page - 1) * $this->recordsPerPage);

        if (is_null($stocks)) {
            return null;
        }

        foreach ($stocks as $stock) {
            // get stock Article
            $stock->setArticle($this->getArticleFromId($stock->getArticleId()));

            // get stock Location
            $stock->setLocation($this->getLocationFromId($stock->getLocationId()));
        }
        return $stocks;
    }

    public function findAllStocksWithArticleLike(string $queryString, ?int $page): ?array
    {   
        // ajouter les location.id et article.id pour pouvoir faire les objets Stock complets...
        $query = 'SELECT stocks.id, location, article, qty
            FROM stocks
            INNER JOIN articles ON stocks.article = articles.id
            WHERE articles.code LIKE :code';

        if (!is_null($page)) {
            $query .= ' LIMIT ' . $this->recordsPerPage . ' OFFSET ' . (($page - 1) * $this->recordsPerPage);
        }

        $req = $this->database->getPDO()->prepare($query);
        $req->execute(['code' => '%' . $queryString . '%']);

        $results = $req->fetchAll();

        if (empty($results)) {
            return null;
        }

        $stocks = [];
        foreach($results as $result) {
            $stock = new Stock();
            $data = [
                'id' => $result['id'],
                'location' => $this->getLocationFromId($result['location']),
                'article' => $this->getArticleFromId($result['article']),
                'qty' => $result['qty']
            ];
            $stock->hydrate($data);
            $stocks[] = $stock;
        }  
        return empty($stocks) ? null : $stocks;
    }

    public function countAllStocksWithArticleLike(string $queryString): int
    {
        $query = 'SELECT COUNT(*) as total FROM stocks
            INNER JOIN articles ON stocks.article = articles.id
            WHERE articles.code LIKE :code';

        $req = $this->database->getPDO()->prepare($query);
        $res = $req->execute(['code' => '%' . $queryString . '%']);
        return ($res === false) ? 0 : $req->fetch()['total'];
    }

    public function confirmRowShipment(Row $row): bool
    {
        $query = 'UPDATE FROM stocks SET qty=qty-:a, reserved=reserved-:b WHERE location=:location';
    }
}
