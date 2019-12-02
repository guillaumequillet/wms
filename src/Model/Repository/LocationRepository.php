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
        return $req->execute([
            'area' => $location->getArea(),
            'aisle' => $location->getAisle(),
            'col' => $location->getCol(),
            'level' => $location->getLevel(),
            'concatenate' => $location->getConcatenate()
        ]);
    }
    
    public function createLocations(array $locations): int
    {
        $reqString = 'INSERT INTO locations(area, aisle, col, level, concatenate) VALUES ';
        $reqValues = [];
        foreach ($locations as $location) {
            $reqValues[] = '("' . join('","', [$location->getArea(), $location->getAisle(), $location->getCol(), $location->getLevel(), $location->getConcatenate()]) . '")';
        }
        $reqString .= join(',', $reqValues);
        $reqString .=  'ON DUPLICATE KEY UPDATE area=area';
        return $this->database->getPDO()->exec($reqString);
    }
}
