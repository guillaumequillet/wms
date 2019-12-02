<?php
declare(strict_types=1);

namespace App\Model\Manager;

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

    public function findAllOutgoings(?string $queryString = null, ?int $page = null): ?array
    {
        $this->repository = new OutgoingRepository($this->database);
        return $this->repository->findWhereAllPaginated(['reference', 'like', "%$queryString%"], $page, 'created_at DESC');
    }

    public function getOutgoing(int $id): ?Outgoing
    {
        $this->repository = new OutgoingRepository($this->database);
        $outgoing = $this->repository->findWhere(['id', '=', $id]);

        if (is_null($outgoing)) {
            return null;
        }

        $this->repository = new UserRepository($this->database);
        $user = $this->repository->findWhere(['id', '=', $outgoing->userId]);

        if (is_null($user)) {
            return null;
        }

        $outgoing->setUser($user);
    
        $this->repository = new RowRepository($this->database);
        $rows = $this->repository->findOutgoingRows($outgoing);

        if (is_null($rows)) {
            return null;
        }

        foreach ($rows as $row) {
            $row->setMovement($outgoing);
        }

        $this->repository = new ArticleRepository($this->database);
        foreach ($rows as $row) {
            $article = $this->repository->findWhere(['id', '=', $row->articleId]);
            if (is_null($article)) {
                return null;
            }
            $row->setArticle($article);
        }

        $this->repository = new LocationRepository($this->database);
        foreach ($rows as $row) {
            $location = $this->repository->findWhere(['id', '=', $row->locationId]);
            if (is_null($location)) {
                return null;
            }
            $row->setLocation($location);
        }

        $outgoing->setRows($rows);
        return $outgoing;
    }

    public function shipOutgoing(Outgoing $outgoing): bool 
    {
        // WE WANT TO REMOVE RESERVATED STOCK AND TO LOWER CURRENT STOCK ACCORDINLY
        
        $this->repository = new StockRepository($this->database);
        foreach($outgoing->getRows() as $row) {
            $this->repository->confirmRowShipment($row);
        }

        /*
        $stocks = [];

        foreach ($outgoing->getRows() as $row) {
            $stock = new Stock();
            $data = [
                'location' => $row->getLocation(),
                'article' => $row->getArticle(),
                'qty' => $row->getQty()
            ];
            $stock->hydrate($data);
            $stocks[] = $stock;
        }

        $this->repository = new StockRepository($this->database);
        $stockRes = $this->repository->createStocks($stocks);            

        if (isset($stockRes) && !$stockRes) {
            return false;
        }        
        return true;
        */
    }

    public function deleteOutgoing(int $id): bool
    {
        // WE MUST UN-RESERVE PRODUCTS FROM STOCK LINES

        $this->repository = new OutgoingRepository($this->database);
        $outgoing = $this->repository->findWhere(['id', '=', $id]);

        if (is_null($outgoing)) {
            return false;
        }

        // we can delete outgoing only if status === "pending"
        if ($outgoing->getStatus() !== 'pending') {
            return false;
        }

        // we delete all related rows
        $this->repository = new RowRepository($this->database);
        $this->repository->deleteOutgoingRows($outgoing);

        // and we delete the movement itself
        $this->repository = new OutgoingRepository($this->database);
        return $this->repository->deleteWhere(['id', '=', $id]);
    }
}
