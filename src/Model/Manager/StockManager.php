<?php
declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entity\Stock;
use App\Model\Repository\StockRepository;
use App\Tool\Token;

class StockManager extends Manager
{
    public function __construct() 
    {
        parent::__construct();
        $this->repository = new StockRepository();
    }

    public function findAllStocks(?string $queryString = null, ?int $page = null): ?array
    {
        if (is_null($page)) {
            $page = 1;
        }

        if (is_null($queryString)) {
            $entities = $this->repository->findAllStocks($page);
            $totalResults = $this->repository->count();
        }

        if (!is_null($queryString)) {
            $entities = $this->repository->findAllStocksWithArticleLike($queryString);
            $totalResults = $this->repository->countAllStocksWithArticleLike($queryString);
        }

        if (is_null($entities)) {
            return null;
        }
        
        return $this->repository->paginate($entities, $totalResults, $page);
    }
}
