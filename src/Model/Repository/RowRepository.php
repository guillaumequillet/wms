<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Row;
use App\Model\Entity\Incoming;

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

    public function deleteIncomingRows(Incoming $incoming): bool
    {
        $req = $this->database->getPDO()->prepare('DELETE FROM `rows` WHERE `movement`=:movement');
        return $req->execute(['movement' => $incoming->getId()]);
    }
}
