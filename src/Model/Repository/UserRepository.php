<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\User;

class UserRepository extends Repository
{
    public function getUserFromUsername(string $username): ?User 
    {
        $stmt = $this->database->getPDO()->prepare('SELECT * FROM users WHERE binary username=:username');
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'App\\Model\\Entity\\User'); 
        $stmt->execute(['username' => $username]);
        $res = $stmt->fetch();
        return ($res ? $res : null);
    }

    public function getCompleteList(): ?array
    {
        $stmt = $this->database->getPDO()->prepare('SELECT * FROM users');
        $stmt->setFetchMode(\PDO::FETCH_CLASS, 'App\\Model\\Entity\\User'); 
        $stmt->execute();
        $res = $stmt->fetchAll();
        return ($res ? $res : null);        
    }

    public function createUser(User $user): bool
    {
        $req = $this->database->getPDO()->prepare('INSERT INTO users(username, password, email, role) 
            VALUES(:username, :password, :email, :role)');
        $res = $req->execute([
            'username' => $user->getUsername(),
            'password' => $user->getPassword(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()
        ]);
        return $res;           
    }
}
