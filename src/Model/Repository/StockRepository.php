<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Stock;

class StockRepository extends Repository
{
    public function createStocks(array $stocks): bool
    {
        $reqString = 'INSERT INTO stocks(location, article, qty) VALUES ';
        $reqValues = [];
        foreach ($stocks as $stock) {
            $reqValues[] = '("' . join('","', [$stock->getLocation()->getId(), $stock->getArticle()->getId(), $stock->getQty()]) . '")';
        }
        $reqString .= join(',', $reqValues);
        $reqString .=  'ON DUPLICATE KEY UPDATE qty=qty+VALUES(qty)';
        $query = $this->database->getPDO()->exec($reqString);        
        return true; // temp
    }
}
