<?php
declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entity\User;
use App\Model\Repository\UserRepository;
use App\Tool\Token;

class LoginManager extends Manager
{
    public function __construct() 
    {
        parent::__construct();
        $this->repository = new UserRepository();
    }

    public function checkLogin(Token $token): string
    {
        $username = $this->superglobalManager->findVariable('post', 'username');
        $password = $this->superglobalManager->findVariable('post', 'password');

        if (is_null($username) || is_null($password)) {
            return '';
        }

        if (!$token->check()) {
            return 'token';
        }

        $user = $this->repository->getUserFromUsername($username);

        if (is_null($user)) {
            return 'user';
        }

        if (!password_verify($password, $user->getPassword())) {
            return 'pwd';
        }

        // user was successfully logged in
        $this->superglobalManager->setVariable('session', 'username', $user->getUsername());
        $this->superglobalManager->setVariable('session', 'role', $user->getRole());

        return 'ok';
    }
}
