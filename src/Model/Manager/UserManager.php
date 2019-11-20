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
        return $this->repository->findWhereAll();
    }

    public function createUser(): bool
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

    public function deleteUser(int $id): bool
    {
        // we can't delete ourselves
        if ($id === $this->superglobalManager->findVariable('session', 'loggedId')) {
            return false;
        }

        // role permissions check
        $role = $this->superglobalManager->findVariable('session', 'role');

        // you can't delete as "simple" user
        if ($role !== 'admin' && $role !== 'superadmin') {
            return false;
        }

        $targetUser = $this->repository->findWhere(['id', '=', $id]);

        // target user must exist
        if (is_null($targetUser)) {
            return false;
        }

        // target user cannot be superadmin
        if ($targetUser->getRole() === 'superadmin') {
            return false;
        }

        // an admin can't delete another admin
        if ($targetUser->getRole() === 'admin' && $role === 'admin') {
            return false;
        }

        return $this->repository->deleteWhere(['id', '=', $id]);
    }
}
