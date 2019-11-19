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

    public function createSingleUser(): bool
    {
        $data = [
            'username' => $this->superglobalManager->findVariable('post', 'username'),
            'email' => $this->superglobalManager->findVariable('post', 'email'),
            'role' => $this->superglobalManager->findVariable('post', 'role'),
            'password' => $this->superglobalManager->findVariable('post', 'password'),
            'confirmPassword' => $this->superglobalManager->findVariable('post', 'confirmPassword')
        ];

        if (in_array(null, $data)) {
            return false;
        }

        if ($data['password'] !== $data['confirmPassword']) {
            return false;
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $user = new User();
        $user->hydrate($data);
        return $this->repository->createUser($user);
    }

    public function updateSingleUser(): bool 
    {
        die('wtf');
    }
}
