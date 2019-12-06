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

    public function findAvailableStocks(string $code, int $quantity): array
    {
        $this->repository = new ArticleRepository($this->database);
        $article = $this->repository->findWhere(['code', '=', $code]);
        $this->repository = new StockRepository($this->database);
        $stocks = $this->repository->findAvailableStock($article, $quantity);
        return is_null($stocks) ? [] : $stocks; 
    }

    public function findAllOutgoings(?string $queryString = null, ?int $page = null): ?array
    {
        $this->repository = new OutgoingRepository($this->database);
        return $this->repository->findWhereAllPaginated(['reference', 'like', "%${queryString}%"], $page, 'created_at DESC');
    }

    public function createOutgoing(): bool
    {
        // if id is not empty, we are updating an existing order
        $currentId = $this->superglobalManager->findVariable('post', 'currentId');
        
        if (is_null($currentId)) {
            return false;
        }

        $currentId = (empty($currentId)) ? null : (int)$currentId;

        $data = [
            'reference' => $this->superglobalManager->findVariable('post', 'reference'),
            'recipient' => $this->superglobalManager->findVariable('post', 'recipient'),
            'address' => $this->superglobalManager->findVariable('post', 'address'),
            'zipcode' => $this->superglobalManager->findVariable('post', 'zipcode'),
            'city' => $this->superglobalManager->findVariable('post', 'city'),
            'country' => $this->superglobalManager->findVariable('post', 'country'),
            'status' => $this->superglobalManager->findVariable('post', 'status')
        ];

        if (in_array(null, $data, true)) {
            return false;
        }

        $articleKeys = $this->superglobalManager->findVariablesLike("post", "/^article[0-9]+$/");
        
        if (empty($articleKeys)) {
            return false;
        }

        $this->repository = new UserRepository($this->database);
        $user = $this->repository->findWhere(['username', '=', $this->superglobalManager->findVariable('session', 'username')]);

        $movement = new Outgoing();
        $rows = [];

        foreach ($articleKeys as $articleKey) {
            preg_match("#^article([0-9]+)$#", $articleKey, $id);
            $article = $this->superglobalManager->findVariable('post', 'article' . $id[1]);
            if (is_null($article))
            {
                return false;                
            }
            $qty = $this->superglobalManager->findVariable('post', 'quantity' . $id[1]);
            if (is_null($qty) || (int)$qty <= 0)
            {
                return false;                
            }
            $location = $this->superglobalManager->findVariable('post', 'location' . $id[1]);
            if (is_null($location))
            {
                return false;                
            }
            
            $this->repository = new ArticleRepository($this->database);
            $article = $this->repository->findWhere(['code', '=', $article]);
            
            if (is_null($article)) {
                return false;
            }

            $this->repository = new LocationRepository($this->database);
            $location = $this->repository->findWhere(['concatenate', '=', $location]);

            if (is_null($location)) {
                return false;
            }

            $row = new Row();

            $rowData = [
                'movement' => $movement,
                'article' => $article,
                'location' => $location,
                'qty' => (int)$qty      
            ];

            $row->hydrate($rowData);
            $rows[] = $row;
        }
       
        $movementData = [
            'provider' => $data['provider'],
            'rows' => $rows,
            'createdAt' => (new \DateTime())->format('Y-m-d H:i:s'),
            'reference' => $data['reference'],
            'user' => $user,
            'status' => $data['status']
        ];
        
        $movement->hydrate($movementData);

        // we create the incoming DB record
        $this->repository = new IncomingRepository($this->database);

        $mvtId = (!is_null($currentId)) ? $currentId : $this->repository->createIncoming($movement);

        if (is_null($mvtId)) {
            return false;
        }

        $movement->setId($mvtId);

        if (!is_null($currentId)) {
            $this->repository->updateIncoming($movement);
        }

        // and the rows themselves
        $this->repository = new RowRepository($this->database);

        // we delete all previous lines
        if (!is_null($currentId)) {
            $this->repository->deleteIncomingRowsForId($currentId);
        }

        // and add the new ones
        $rwsRes = $this->repository->createIncomingRows($movement->getRows());

        if (!$rwsRes) {
            return false;
        }

        // if status is received, we create the stocks
        if ($data['status'] === 'received') {
            $this->receiveIncoming($movement);
        }

        return true;
    }

    public function deleteOutgoing(int $id): bool
    {
        $this->repository = new OutgoingRepository($this->database);
        $outgoing = $this->repository->findWhere(['id', '=', $id]);

        if (is_null($outgoing)) {
            return false;
        }

        // we can delete outgoings only if status === "pending"
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
