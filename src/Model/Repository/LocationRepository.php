<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Location;

class LocationRepository extends Repository
{
    public function createLocation(Location $location): bool
    {
        $req = $this->database->getPDO()->prepare('INSERT INTO locations(area, aisle, col, level, concatenate) 
            VALUES(:area, :aisle, :col, :level, :concatenate)');
        $res = $req->execute([
            'area' => $location->getArea(),
            'aisle' => $location->getAisle(),
            'col' => $location->getCol(),
            'level' => $location->getLevel(),
            'concatenate' => $location->getConcatenate()
        ]);
        return $res;   
    }
}
