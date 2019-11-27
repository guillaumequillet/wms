<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Article;
use App\Model\Entity\Location;
use App\Model\Entity\Stock;

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

    public function findAllStocks(?int $page): ?array
    {
        $stocks = $this->findWhereAll([], $limit = $this->recordsPerPage, $offset = ($page - 1) * $this->recordsPerPage);

        if (is_null($stocks)) {
            return null;
        }

        foreach ($stocks as $stock) {
            // get stock Article
            $req = $this->database->getPDO()->prepare('SELECT * FROM articles WHERE id=:id LIMIT 1');
            $req->setFetchMode(\PDO::FETCH_CLASS, Article::class);
            $req->execute(['id' => $stock->getArticleId()]);
            $res = $req->fetch();
 
            if (empty($res)) {
                return null;
            }
            $stock->setArticle($res);

            // get stock Location
            $req = $this->database->getPDO()->prepare('SELECT * FROM locations WHERE id=:id LIMIT 1');
            $req->setFetchMode(\PDO::FETCH_CLASS, Location::class);
            $req->execute(['id' => $stock->getLocationId()]);
            $res = $req->fetch();
 
            if (empty($res)) {
                return null;
            }
            $stock->setLocation($res);
        }
        return $stocks;
    }

    public function findAllStocksWithArticleLike(string $queryString, ?int $page): ?array
    {   
        
    }

    public function countAllStocksWithArticleLike(string $queryString): int
    {

    }
}
