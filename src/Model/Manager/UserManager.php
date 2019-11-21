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

    public function getUser(int $id): ?User
    {
        return $this->repository->findWhere(['id', '=', $id]);
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

        if (in_array(null, $data, true)) {
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

    public function checkPermission(int $id, bool $allowSelf): bool
    {
        // if target is the user
        if (!$allowSelf && ($id === (int)$this->superglobalManager->findVariable('session', 'loggedId'))) {
            return false;
        }

        if ($allowSelf && ($id === (int)$this->superglobalManager->findVariable('session', 'loggedId'))) {
            return true;
        }

        // it target is not the user : role permissions check
        $role = $this->superglobalManager->findVariable('session', 'role');

        // you can't admin as "simple" user
        if ($role !== 'admin' && $role !== 'superadmin') {
            return false;
        }

        $targetUser = $this->repository->findWhere(['id', '=', $id]);

        // target user must exist
        if (is_null($targetUser)) {
            return false;
        }

        // only superadmin can target a superadmin
        if ($role !== 'superadmin' && $targetUser->getRole() === 'superadmin') {
            return false;
        }

        // an admin can't affect another admin
        if ($targetUser->getRole() === 'admin' && $role === 'admin') {
            return false;
        }

        return true;
    }

    public function deleteUser(int $id): bool
    {
        if (!$this->checkPermission($id, false)) {
            return false;
        }

        return $this->repository->deleteWhere(['id', '=', $id]);
    }

    public function updateUser(int $id): bool
    {
        if (!$this->checkPermission($id, true)) {
            return false;
        }

        $user = new User();
        
        $data = [
            'username' => $this->superglobalManager->findVariable('post', 'username'),
            'email' => $this->superglobalManager->findVariable('post', 'email'),
            'role' => $this->superglobalManager->findVariable('post', 'role'),
            'password' => $this->superglobalManager->findVariable('post', 'newPassword'),
            'confirmPassword' => $this->superglobalManager->findVariable('post', 'newPasswordConfirm')
        ];

        // to ensure that role was not modified for self
        $loggedId = $this->superglobalManager->findVariable('session', 'loggedId');
        $role = $this->superglobalManager->findVariable('session', 'role'); 
        if ($id === $loggedId && $role !== $data['role']) {
            return false;
        }  

        if (in_array(null, $data, true)) {
            return false;
        }

        if ($data['password'] != $data['confirmPassword']) {
            return false;
        }

        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $newPassword = true;
        }

        if (empty($data['password'])) {
            $data['password'] = '';
            $newPassword = false;
        }

        $data['id'] = $id;
        $user->hydrate($data);

        return $this->repository->updateUser($user, $newPassword);
    }
}
