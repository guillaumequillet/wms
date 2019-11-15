<?php
declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entity\User;
use App\Model\Repository\UserRepository;

class UserManager extends Manager
{
    public function __construct() 
    {
        parent::__construct();
        $this->repository = new UserRepository();
    }

    public function checkLogin(): bool
    {
        $username = $this->superglobalManager->findVariable('post', 'username');
        $password = $this->superglobalManager->findVariable('post', 'password');

        if (is_null($username) || is_null($password)) {
            return false;
        }

        $user = $this->repository->getUserFromUsername($username);

        if (is_null($user)) {
            return false;
        }

        $hashedPwd = $res->getPassword();
        if (!password_verify($password, $hashedPwd)) {
            return false;
        }

        // user was successfully logged in
        $this->superglobalManager->setVariable('session', 'username', $user->getUsername());
        $this->superglobalManager->setVariable('session', 'profile', $user->getProfile());

        return true;
    }
}
