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

    public function findLocationCount(): int
    {
        $reqString = 'SELECT COUNT(*) as total FROM locations';
        $req = $this->database->getPDO()->prepare($reqString);
        $res = $req->execute();
        return ($res === false) ? 0 : $req->fetch()['total'];
    }

    public function findLocations(?int $page = null, ?int $resultsPerPage = null): ?array
    {
        $req = 'SELECT * FROM locations';
        $fields = [];

        if (!is_null($page) && !is_null($resultsPerPage)) {
            $req .= ' LIMIT :limit OFFSET :offset';
            $fields['limit'] = $resultsPerPage;
            $fields['offset'] = $resultsPerPage * ($page - 1);
        }

        $stmt = $this->database->getPDO()->prepare($req);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, Location::class); 
        $stmt->execute($fields);
        $res = $stmt->fetchAll();
        return ($res === false) ? null : $res;        
    }

    public function findPaginatedLocations(int $page): ?array
    {
        $resultsPerPage = 10;
        $entities = $this->findLocations($page, $resultsPerPage);
        $totalResults = $this->findLocationCount();
        $currentPage = $page;

        return $this->paginate($entities, $totalResults, $currentPage, $resultsPerPage);
    }
}
