<?php
declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entity\Incoming;
use App\Model\Entity\Row;
use App\Model\Entity\Stock;
use App\Model\Repository\ArticleRepository;
use App\Model\Repository\LocationRepository;
use App\Model\Repository\MovementRepository;
use App\Model\Repository\UserRepository;
use App\Model\Repository\RowRepository;
use App\Model\Repository\StockRepository;
use App\Tool\Database;
use App\Tool\Token;

class MovementManager extends Manager
{
    private $database;

    public function __construct() 
    {
        parent::__construct();
        $this->database = new Database();
    }

    public function createIncoming(): bool
    {
        $data = [
            'provider' => $this->superglobalManager->findVariable('post', 'provider'),
            'reference' => $this->superglobalManager->findVariable('post', 'reference'),
            'status' => $this->superglobalManager->findVariable('post', 'status')
        ];

        if (in_array(null, $data, true)) {
            return false;
        }

        // doit être ajouté dans le superglobalManager
        $articleKeys = array_values(preg_grep("/^article[0-9]+$/", array_keys($_POST)));    
        
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
            'status' => $data['status'],
            'user' => $user
        ];
        
        $movement->hydrate($movementData);

        // we create the incoming DB record
        $this->repository = new MovementRepository($this->database);
        $mvtID = $this->repository->createIncoming($movement);

        if (is_null($mvtID)) {
            return false;
        }

        $movement->setId($mvtID);

        // and the rows themselves
        $this->repository = new RowRepository($this->database);
        $rwsRes = $this->repository->createIncomingRows($movement->getRows());

        if (!$rwsRes) {
            return false;
        }

        // if status === received, we create the stock
        if ($movement->getStatus() === 'received') {
            $stocks = [];

            foreach ($movement->getRows() as $row) {
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
        }

        if (isset($stockRes) && !$stockRes) {
            return false;
        }

        return true;
    }
}
