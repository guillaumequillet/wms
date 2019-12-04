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

    public function findAvailableStock(Article $article, int $quantity, array $alreadyReserved): ?array
    {
        // do we have enough stock at all ?
        $query = 'SELECT SUM(qty) as total FROM stocks WHERE article=:id';
        $req = $this->database->getPDO()->prepare($query);
        $res = $req->execute(['id' => $article->getId()]);

        if (!$res) {
            return null;
        }

        $totalQty = $req->fetch()['total'];
        if ($totalQty < $quantity) {
            return null;
        }

        // do we have enough unreserved stock ?
        $query = 'SELECT SUM(rows.qty) as total FROM `rows`';
        $query .= ' INNER JOIN outgoings ON rows.movement = outgoings.id';
        $query .= ' WHERE rows.article=:id AND rows.type="outgoing" AND outgoings.status="pending"';
        $req = $this->database->getPDO()->prepare($query);
        $res = $req->execute(['id' => $article->getId()]); 
        
        if (!$res) {
            return null;
        }

        $reservedQty = $req->fetch()['total'];
        if ($totalQty - $reservedQty < $quantity) {
            return null;
        }

        if ($totalQty - $reservedQty - array_sum(array_values($alreadyReserved)) < $quantity) {
            return null;
        }

        // we have enough space to serve the request

        // we get every stock 
        $query = 'SELECT locations.concatenate, stocks.qty FROM `stocks`';
        $query .= 'INNER JOIN locations ON locations.id=stocks.location';
        $query .= ' WHERE stocks.article=:id';
        $req = $this->database->getPDO()->prepare($query);
        $res = $req->execute(['id' => $article->getID()]);

        if (!$res) {
            return false;
        }
        $stocks = $req->fetchAll();

        // we get every related reserved stock
        $query = 'SELECT locations.concatenate, rows.qty FROM `rows`';
        $query .= ' INNER JOIN locations ON locations.id=rows.location';
        $query .= ' INNER JOIN outgoings ON rows.movement=outgoings.id';
        $query .= ' WHERE rows.article=:id AND rows.type="outgoing" AND outgoings.status="pending"';
        $req = $this->database->getPDO()->prepare($query);
        $res = $req->execute(['id' => $article->getID()]);

        if (!$res) {
            return false;
        }
        $reservedRows = $req->fetchAll();
        $reserved = [];

        foreach($reservedRows as $reservedRow) {
            $key = $reservedRow['concatenate'];
            $reserved[$key] = $reservedRow['qty'];
            if (array_key_exists($key, $alreadyReserved)) {
                $reserved[$key] -= $alreadyReserved[$key];
            }
        }

        $returnedStocks = [];
        $currentSum = 0;

        foreach ($stocks as $stock) {
            $reservedQty = 0;
 
            if (isset($reserved[$stock['concatenate']])) {
                $reservedQty = $reserved[$stock['concatenate']];
            }
 
            $availableQty = $stock['qty'] - $reservedQty;
            $missingQty = $quantity - $currentSum;
 
            if ($availableQty === $missingQty) {
                $returnedStocks[] = [
                    'location' => $stock['concatenate'],
                    'availableQty' => $stock['qty']
                ];
                break;
            }

            if ($availableQty > $missingQty) {
                $returnedStocks[] = [
                    'location' => $stock['concatenate'],
                    'availableQty' => $missingQty
                ];
                break;
            }

            if ($availableQty < $missingQty) {
                $returnedStocks[] = [
                    'location' => $stock['concatenate'],
                    'availableQty' => $availableQty
                ];
                $currentSum += $availableQty;
            }
        }

        return empty($returnedStocks) ? null : $returnedStocks;
    }

    // this function can return several Stocks to sastify the quantity
    // public function findAvailableStock(Article $article, int $quantity, array $alreadyReserved): ?array
    // {
    //     $this->database->getPDO()->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    //     $query = 'SELECT (SUM(qty) - SUM(reserved)) as isAvailable from stocks WHERE article=:article GROUP BY article';
    //     $req = $this->database->getPDO()->prepare($query);
    //     $res = $req->execute(['article' => $article->getID()]);
        
    //     if (!$res) {
    //         return null;
    //     }

    //     if ($req->fetch()['isAvailable'] < $quantity) {
    //         return null;
    //     }

    //     $query = 'SELECT locations.concatenate, (stocks.qty - stocks.reserved) as availableQty FROM stocks';
    //     $query .= ' INNER JOIN locations ON stocks.location = locations.id';
    //     $query .= ' WHERE stocks.article=:article AND (stocks.qty - stocks.reserved) > 0';
    //     $req = $this->database->getPDO()->prepare($query);
    //     $res = $req->execute(['article' => $article->getID()]);

    //     if (!$res) {
    //         return null;
    //     }

    //     $availableStocks = [];
    //     $servedQuantity = 0;

    //     foreach($req->fetchAll() as $availableStock) {
    //         $remaining = $quantity - $servedQuantity;

    //         if ($availableStock['availableQty'] === $remaining) {
    //             $availableStocks[] = $availableStock;
    //             break;
    //         }

    //         if ($availableStock['availableQty'] < $remaining) {
    //             $availableStocks[] = $availableStock;
    //             $servedQuantity += $availableStock['availableQty'];
    //         }

    //         if ($availableStock['availableQty'] > $remaining) {
    //             $availableStocks[] = ['location' => $availableStock['location'], 'availableQty' => $remaining];
    //             break;
    //         }
    //     }

    //     return empty($availableStocks) ? null : $availableStocks;
    // }
}
