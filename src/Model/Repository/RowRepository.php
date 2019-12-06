<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Row;
use App\Model\Entity\Incoming;
use App\Model\Entity\Outgoing;

class RowRepository extends Repository
{
    public function createIncomingRows(array $rows): bool
    {
        $reqString = 'INSERT INTO `rows` (`id`, `movement`, `type`, `article`, `location`, `qty`) VALUES ';
        $reqValues = [];
        foreach ($rows as $row) {
            $reqValues[] = "(NULL, '" . join("','", [$row->getMovement()->getId(), 'incoming', $row->getArticle()->getId(), $row->getLocation()->getId(), $row->getQty()]) . "')";
        }
        $reqString .= join(',', $reqValues);
        $res = $this->database->getPDO()->exec($reqString); 
        return ($res === count($rows));
    }

    public function createOutgoingRows(array $rows): bool
    {
        $reqString = 'INSERT INTO `rows` (`id`, `movement`, `type`, `article`, `location`, `qty`) VALUES ';
        $reqValues = [];
        foreach ($rows as $row) {
            $reqValues[] = "(NULL, '" . join("','", [$row->getMovement()->getId(), 'outgoing', $row->getArticle()->getId(), $row->getLocation()->getId(), $row->getQty()]) . "')";
        }
        $reqString .= join(',', $reqValues);
        $res = $this->database->getPDO()->exec($reqString); 
        return ($res === count($rows));
    }


    public function deleteIncomingRows(Incoming $incoming): bool
    {
        $req = $this->database->getPDO()->prepare('DELETE FROM `rows` WHERE `type`="incoming" AND `movement`=:movement');
        return $req->execute(['movement' => $incoming->getId()]);
    }

    public function deleteOutgoingRows(Outgoing $outgoing): bool
    {
        $req = $this->database->getPDO()->prepare('DELETE FROM `rows` WHERE `type`="outgoing" AND `movement`=:movement');
        return $req->execute(['movement' => $outgoing->getId()]);
    }

    public function findIncomingRows(Incoming $incoming): ?array
    {
        $req = $this->database->getPDO()->prepare('SELECT * FROM `rows` WHERE `type`="incoming" AND `movement`=:movement');
        $req->setFetchMode(\PDO::FETCH_CLASS, Row::class); 
        $req->execute(['movement' => $incoming->getId()]);
        $res = $req->fetchAll();
        return ($res === false) ? null : $res;
    }

    public function findOutgoingRows(Outgoing $outgoing): ?array
    {
        $req = $this->database->getPDO()->prepare('SELECT * FROM `rows` WHERE `type`="outgoing" AND `movement`=:movement');
        $req->setFetchMode(\PDO::FETCH_CLASS, Row::class); 
        $req->execute(['movement' => $outgoing->getId()]);
        $res = $req->fetchAll();
        return ($res === false) ? null : $res;
    }

    public function deleteIncomingRowsForId(int $id): bool
    {
        $req = $this->database->getPDO()->prepare('DELETE FROM `rows` WHERE `type`="incoming" AND `movement`=:movement');
        return $req->execute(['movement' => $id]);
    }
}
