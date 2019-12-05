<?php
declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entity\Article;
use App\Model\Entity\Outgoing;
use App\Model\Entity\Row;
use App\Model\Entity\Stock;
use App\Model\Repository\ArticleRepository;
use App\Model\Repository\LocationRepository;
use App\Model\Repository\OutgoingRepository;
use App\Model\Repository\UserRepository;
use App\Model\Repository\RowRepository;
use App\Model\Repository\StockRepository;
use App\Tool\Database;
use App\Tool\Token;

class OutgoingManager extends Manager
{
    private $database;

    public function __construct() 
    {
        parent::__construct();
        $this->database = new Database();
    }

    public function findAvailableStocks(string $code, int $quantity, array $alreadyReserved = []): array
    {
        $this->repository = new ArticleRepository($this->database);
        $article = $this->repository->findWhere(['code', '=', $code]);
        $this->repository = new StockRepository($this->database);
        $stocks = $this->repository->findAvailableStock($article, $quantity, $alreadyReserved);
        return is_null($stocks) ? [] : $stocks; 
    }

    public function findAllOutgoings(?string $queryString = null, ?int $page = null): ?array
    {
        // $this->repository = new ArticleRepository();
        // $article = $this->repository->findWhere(['code', '=', 'CODE1']);
        // $quantity = 10;
        // $alreadyReserved = [
        //     // 'D-001-001-A' => 1,
        //     // 'D-001-002-A' => 2,
        //     // 'D-001-003-A' => 1
        // ];
        // $this->repository = new StockRepository();
        // $res = $this->repository->findAvailableStock($article, $quantity, $alreadyReserved);

        // die(dump($res));

        $this->repository = new OutgoingRepository($this->database);
        return $this->repository->findWhereAllPaginated(['reference', 'like', "%${queryString}%"], $page, 'created_at DESC');
    }

    public function createOutgoing(): string
    {

    }
}
