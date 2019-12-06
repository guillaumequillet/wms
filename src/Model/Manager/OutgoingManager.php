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
        
        if (is_null($articleKeys)) {
            return false;
        }

        $this->repository = new UserRepository($this->database);
        $user = $this->repository->findWhere(['username', '=', $this->superglobalManager->findVariable('session', 'username')]);

        $movement = new Outgoing();
        $rows = [];

        foreach ($articleKeys as $articleKey) {
            preg_match("#^article([0-9]+)$#", $articleKey, $id);
            $currentLine = $id[1];

            $article = $this->superglobalManager->findVariable('post', 'article' . $currentLine);
            if (is_null($article))
            {
                return false;                
            }
            $qty = $this->superglobalManager->findVariable('post', 'globalQty' . $currentLine);
            if (is_null($qty) || (int)$qty <= 0)
            {
                return false;                
            }
            
            $this->repository = new ArticleRepository($this->database);
            $article = $this->repository->findWhere(['code', '=', $article]);
            
            if (is_null($article)) {
                return false;
            }

            $locationKeys = $this->superglobalManager->findVariablesLike("post", "#^location" . $currentLine . "_([0-9]+)$#");

            if (is_null($locationKeys)) {
                return false;
            }

            foreach ($locationKeys as $locationKey) {
                preg_match("#^location" . $currentLine . "_([0-9]+)$#", $locationKey, $id);
                
                $postLocation = $this->superglobalManager->findVariable('post', 'location' . $currentLine . "_" . $id[1]);
                if (is_null($postLocation))
                {
                    return false;                
                }
                $postQty = $this->superglobalManager->findVariable('post', 'qty' . $currentLine . "_" . $id[1]);
                if (is_null($postQty) || (int)$postQty <= 0)
                {
                    return false;                
                }               

                $this->repository = new LocationRepository($this->database);
                $location = $this->repository->findWhere(['concatenate', '=', $postLocation]);
    
                if (is_null($location)) {
                    return false;
                }                

                $row = new Row();

                $rowData = [
                    'movement' => $movement,
                    'article' => $article,
                    'location' => $location,
                    'qty' => (int)$postQty      
                ];
    
                $row->hydrate($rowData);
                $rows[] = $row;
            }
        }
       
        $movementData = [
            'rows' => $rows,
            'createdAt' => (new \DateTime())->format('Y-m-d H:i:s'),
            'reference' => $data['reference'],
            'user' => $user,
            'status' => $data['status'],
            'recipient' => $data['recipient'],
            'address' => $data['address'],
            'zipcode' => $data['zipcode'],
            'city' => $data['city'],
            'country' => $data['country']
        ];
        
        $movement->hydrate($movementData);

        // we create the outgoing DB record
        $this->repository = new OutgoingRepository($this->database);

        $mvtId = (!is_null($currentId)) ? $currentId : $this->repository->createOutgoing($movement);

        if (is_null($mvtId)) {
            return false;
        }

        $movement->setId($mvtId);

        if (!is_null($currentId)) {
            $this->repository->updateOutgoing($movement);
        }

        // and the rows themselves
        $this->repository = new RowRepository($this->database);

        // we delete all previous lines
        if (!is_null($currentId)) {
            $this->repository->deleteOutgoingRowsForId($currentId);
        }

        // and add the new ones
        $rwsRes = $this->repository->createOutgoingRows($movement->getRows());

        if (!$rwsRes) {
            return false;
        }

        // if status is received, we create the stocks
        if ($data['status'] === 'shipped') {
            $this->shipOutgoing($movement);
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

    public function shipOutgoing(Outgoing $outgoing): bool
    {
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
        $stockRes = $this->repository->pickFromStocks($stocks);            

        if (isset($stockRes) && !$stockRes) {
            return false;
        }        
        return true;
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

    public function unreserve(string $code, int $outgoingId): bool
    {
        $this->repository = new ArticleRepository();
        $article = $this->repository->findWhere(['code', '=', $code]);
        $this->repository = new OutgoingRepository();
        $outgoing = $this->repository->findWhere(['id', '=', $outgoingId]);
        return $this->repository->unreserve($article, $outgoing);
    }
}
