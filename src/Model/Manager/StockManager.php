<?php
declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Repository\StockRepository;

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
            $entities = $this->repository->findAllStocksWithArticleLike($queryString, $page);
            $totalResults = $this->repository->countAllStocksWithArticleLike($queryString);
        }

        if (is_null($entities)) {
            return null;
        }
        
        return $this->repository->paginate($entities, $totalResults, $page);
    }

    public function findReservedStocks(array $stocks): ?array
    {
        return $this->repository->findReservedStocks($stocks);
    }
}
