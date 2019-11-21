<?php
declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entity\Movement;
use App\Model\Repository\MovementRepository;
use App\Tool\Token;

class MovementManager extends Manager
{
    public function __construct() 
    {
        parent::__construct();
        $this->repository = new MovementRepository();
    }
}
