<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Incoming;

class IncomingRepository extends Repository
{
    public function findAllIncomings(int $page): ?array
    {
        
    }

    public function createIncoming(Incoming $movement): ?int
    {
        // we create the incoming record
        $req = $this->database->getPDO()->prepare('INSERT INTO incomings(created_at, reference, user, provider, status) 
        VALUES(:createdAt, :reference, :user, :provider, :status)');
        $res = $req->execute([
            'createdAt' => $movement->getCreatedAt(),
            'reference' => $movement->getReference(),
            'user' => $movement->getUser()->getId(),
            'provider' => $movement->getProvider(),
            'status' => $movement->getStatus()
        ]);

        if (!$res) {
            return null;
        }
        
        // and return the last ID created
        $res = $this->database->getPDO()->query('SELECT id FROM incomings ORDER BY id DESC LIMIT 1');
        if (!$res) {
            return null;
        }
        return $res->fetch()['id'];
    }
}
