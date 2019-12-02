<?php
declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entity\Incoming;
use App\Model\Entity\Row;
use App\Model\Entity\Stock;
use App\Model\Repository\ArticleRepository;
use App\Model\Repository\LocationRepository;
use App\Model\Repository\IncomingRepository;
use App\Model\Repository\UserRepository;
use App\Model\Repository\RowRepository;
use App\Model\Repository\StockRepository;
use App\Tool\Database;
use App\Tool\Token;

class IncomingManager extends Manager
{
    private $database;

    public function __construct() 
    {
        parent::__construct();
        $this->database = new Database();
    }

    public function findAllIncomings(?string $queryString = null, ?int $page = null): ?array
    {
        $this->repository = new IncomingRepository($this->database);
        return $this->repository->findWhereAllPaginated(['reference', 'like', "%$queryString%"], $page);
    }

    public function createIncoming(): bool
    {
        // if id is not empty, we are updating an existing order
        $currentId = $this->superglobalManager->findVariable('post', 'currentId');
        
        if (is_null($currentId)) {
            return false;
        }

        $currentId = (empty($currentId)) ? null : (int)$currentId;

        $data = [
            'provider' => $this->superglobalManager->findVariable('post', 'provider'),
            'reference' => $this->superglobalManager->findVariable('post', 'reference'),
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

        $movement = new Incoming();
        $rows = [];

        foreach ($articleKeys as $articleKey) {
            preg_match("#^article([0-9]+)$#", $articleKey, $id);
            if (is_null($article = $this->superglobalManager->findVariable('post', 'article' . $id[1])))
            {
                return false;                
            }
            if (is_null($qty = $this->superglobalManager->findVariable('post', 'quantity' . $id[1])))
            {
                return false;                
            }
            if (is_null($location = $this->superglobalManager->findVariable('post', 'location' . $id[1])))
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

    public function receiveIncoming(Incoming $incoming): bool 
    {
        $stocks = [];

        foreach ($incoming->getRows() as $row) {
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
    }

    public function deleteIncoming(int $id): bool
    {
        $this->repository = new IncomingRepository($this->database);
        $incoming = $this->repository->findWhere(['id', '=', $id]);

        if (is_null($incoming)) {
            return false;
        }

        // we can delete incomings only if status === "pending"
        if ($incoming->getStatus() !== 'pending') {
            return false;
        }

        // we delete all related rows
        $this->repository = new RowRepository($this->database);
        $this->repository->deleteIncomingRows($incoming);

        // and we delete the movement itself
        $this->repository = new IncomingRepository($this->database);
        return $this->repository->deleteWhere(['id', '=', $id]);
    }

    public function getIncoming(int $id): ?Incoming
    {
        $this->repository = new IncomingRepository($this->database);
        $incoming = $this->repository->findWhere(['id', '=', $id]);

        if (is_null($incoming)) {
            return null;
        }

        $this->repository = new UserRepository($this->database);
        $user = $this->repository->findWhere(['id', '=', $incoming->userId]);

        if (is_null($user)) {
            return null;
        }

        $incoming->setUser($user);
    
        $this->repository = new RowRepository($this->database);
        $rows = $this->repository->findIncomingRows($incoming);

        if (is_null($rows)) {
            return null;
        }

        foreach ($rows as $row) {
            $row->setMovement($incoming);
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

        $incoming->setRows($rows);
        return $incoming;
    }
}
