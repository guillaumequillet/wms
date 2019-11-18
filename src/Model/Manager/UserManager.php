<?php
declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entity\User;
use App\Model\Repository\UserRepository;
use App\Tool\Token;

class UserManager extends Manager
{
    public function __construct() 
    {
        parent::__construct();
        $this->repository = new UserRepository();
    }

    public function getUsersList(): ?array 
    {
        return $this->repository->getCompleteList();
    }
}
