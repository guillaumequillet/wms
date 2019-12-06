<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Outgoing;
use App\Model\Entity\Article;

class OutgoingRepository extends Repository
{
    public function createOutgoing(Outgoing $movement): ?int
    {
        // we create the outgoing record
        $req = $this->database->getPDO()->prepare('INSERT INTO outgoings(created_at, reference, user, recipient, address, zipcode, city, country, status) 
        VALUES(:createdAt, :reference, :user, :recipient, :address, :zipcode, :city, :country, :status)');
        $res = $req->execute([
            'createdAt' => $movement->getCreatedAt(),
            'reference' => $movement->getReference(),
            'user' => $movement->getUser()->getId(),
            'recipient' => $movement->getRecipient(),
            'address' => $movement->getAddress(),
            'zipcode' => $movement->getZipcode(),
            'city' => $movement->getCity(),
            'country' => $movement->getCountry(),
            'status' => $movement->getStatus()
        ]);

        if (!$res) {
            return null;
        }
        
        // and return the last ID created
        $res = $this->database->getPDO()->query('SELECT id FROM outgoings ORDER BY id DESC LIMIT 1');
        if (!$res) {
            return null;
        }
        return $res->fetch()['id'];
    }

    public function updateOutgoing(Outgoing $movement): bool
    {
        $req = $this->database->getPDO()->prepare('UPDATE outgoings 
            SET id=:id, created_at=:createdAt, reference=:reference, user=:user, recipient=:recipient, address=:address, zipcode=:zipcode, city=:city, country=:country, status=:status 
            WHERE id=:moveId');
        return $req->execute([
            'id' => $movement->getId(),
            'createdAt' => $movement->getCreatedAt(),
            'reference' => $movement->getReference(),
            'user' => $movement->getUser()->getId(),
            'recipient' => $movement->getRecipient(),
            'address' => $movement->getAddress(),
            'zipcode' => $movement->getZipcode(),
            'city' => $movement->getCity(),
            'country' => $movement->getCountry(),
            'status' => $movement->getStatus(),
            'moveId' => $movement->getId()
        ]);        
    }    

    public function unreserve(Article $article, Outgoing $outgoing): bool
    {
        $req = $this->database->getPDO()->prepare('DELETE FROM `rows` 
            WHERE movement=:id AND type="outgoing" AND article=:article');
        return $req->execute([
            'id' => $outgoing->getId(),
            'article' => $article->getId()
        ]);
    }
}
