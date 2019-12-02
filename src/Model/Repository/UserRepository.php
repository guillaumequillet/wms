<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\User;

class UserRepository extends Repository
{
    public function getUserFromUsername(string $username): ?User 
    {
        $stmt = $this->database->getPDO()->prepare('SELECT * FROM users WHERE binary username=:username');
        $stmt->setFetchMode(\PDO::FETCH_CLASS, User::class); 
        $stmt->execute(['username' => $username]);
        $res = $stmt->fetch();
        return ($res ? $res : null);
    }

    public function createUser(User $user): bool
    {
        $req = $this->database->getPDO()->prepare('INSERT INTO users(username, password, email, role) 
            VALUES(:username, :password, :email, :role)');
        return $req->execute([
            'username' => $user->getUsername(),
            'password' => $user->getPassword(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()
        ]);
    }

    public function updateUser(User $user, bool $newPassword): bool
    {
        $queryString = 'UPDATE users 
        SET username=:username, ' . ($newPassword ? 'password=:password, ' : '') . 'email=:email, role=:role 
        WHERE id=:id';

        $params = [
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'role' => $user->getRole(),
            'id' => $user->getId()
        ];

        if ($newPassword) {
            $params['password'] = $user->getPassword();
        }

        $req = $this->database->getPDO()->prepare($queryString);

        return $req->execute($params);
    }
}
