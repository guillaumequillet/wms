<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Incoming;

class IncomingRepository extends Repository
{
    public function createIncoming(Incoming $incoming): ?int
    {
        // we create the incoming record
        $req = $this->database->getPDO()->prepare('INSERT INTO incomings(created_at, reference, user, provider, status) 
        VALUES(:createdAt, :reference, :user, :provider, :status)');
        $res = $req->execute([
            'createdAt' => $incoming->getCreatedAt(),
            'reference' => $incoming->getReference(),
            'user' => $incoming->getUser()->getId(),
            'provider' => $incoming->getProvider(),
            'status' => $incoming->getStatus()
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

    public function updateIncoming(Incoming $incoming): bool
    {
        $req = $this->database->getPDO()->prepare('UPDATE incomings SET id=:id, created_at=:createdAt, reference=:reference, user=:user, provider=:provider, status=:status WHERE id=:moveId');
        return $req->execute([
            'id' => $incoming->getId(),
            'createdAt' => $incoming->getCreatedAt(),
            'reference' => $incoming->getReference(),
            'user' => $incoming->getUser()->getId(),
            'provider' => $incoming->getProvider(),
            'status' => $incoming->getStatus(),
            'moveId' => $incoming->getId()
        ]);        
    }
}
