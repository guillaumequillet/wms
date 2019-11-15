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
}
