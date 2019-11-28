<?php
declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entity\Location;
use App\Model\Repository\LocationRepository;
use App\Tool\ParserCSV;

class LocationManager extends Manager
{
    public function __construct() 
    {
        parent::__construct();
        $this->repository = new LocationRepository();
    }
   
    public function createSingleLocation(): bool
    {
        $location = new Location(); 

        $data = [
            'area' => $this->superglobalManager->findVariable('post', 'area'),
            'aisle' => $this->superglobalManager->findVariable('post', 'aisle'),
            'col' => $this->superglobalManager->findVariable('post', 'col'),
            'level' => $this->superglobalManager->findVariable('post', 'level')
        ];

        if (in_array(null, $data, true)) {
            return false;
        }

        foreach($data as $k=>$v) {
            if (preg_match('/^[a-z]$/', $v)) {
                $data[$k] = strtoupper($v);
            }
            if (preg_match('/^[0-9]{1,3}$/', $v)) {
                $data[$k] = $location->intToString((int)$v);
            }
        }

        $location->hydrate($data);
        $location->setConcatenate();
        return $this->repository->createLocation($location);
    }

    public function createIntervalLocations(): string
    {
        $fields = [
            'area' => $this->superglobalManager->findVariable("post", "intervalArea"),
            'fromAisle' => $this->superglobalManager->findVariable("post", "fromAisle"),
            'toAisle' => $this->superglobalManager->findVariable("post", "toAisle"),
            'fromCol' => $this->superglobalManager->findVariable("post", "fromCol"),
            'toCol' => $this->superglobalManager->findVariable("post", "toCol"),
            'fromLevel' => $this->superglobalManager->findVariable("post", "fromLevel"),
            'toLevel' => $this->superglobalManager->findVariable("post", "toLevel")
        ];

        if (in_array(null, $fields, true)) {
            return "none";
        }

        $types = [];
        foreach ($fields as $k=>$v) {
            $types[$k] = is_numeric($v) ? "int" : "string";
        }

        if ($types['fromAisle'] !== $types['toAisle']) {
            return "none";
        }

        if ($types['fromCol'] !== $types['toCol']) {
            return "none";
        }

        if ($types['fromLevel'] !== $types['toLevel']) {
            return "none";
        }

        $locations = [];

        foreach (range($fields['fromAisle'], $fields['toAisle']) as $aisle) {
            foreach (range($fields['fromCol'], $fields['toCol']) as $col) {
                foreach (range($fields['fromLevel'], $fields['toLevel']) as $level) {
                    $location = new Location();
                    $data = [
                        'area' => (string)$fields['area'],
                        'aisle' => (string)$aisle,
                        'col' => (string)$col,
                        'level' => (string)$level                        
                    ];

                    foreach($data as $k=>$v) {
                        if (preg_match('/^[a-z]$/', $v)) {
                            $data[$k] = strtoupper($v);
                        }
                        if (preg_match('/^[0-9]{1,3}$/', $v)) {
                            $data[$k] = $location->intToString((int)$v);
                        }
                    }                    

                    $location->hydrate($data);
                    $location->setConcatenate();
                    $locations[] = $location;
                }
            }
        }

        $res = $this->repository->createLocations($locations);

        if ($res === count($locations)) {
            return "fullInterval";
        }

        if ($res === 0) {
            return "noneInterval";
        }

        return "partialInterval";
    }

    public function createLocations(): string
    {
        if ($this->superglobalManager->findFile('locationFile')) {
            $filename = $this->superglobalManager->findFile('locationFile')["tmp_name"];
        }

        if (!isset($filename) || $filename === "") {
            return false;
        }

        $csvFile = new ParserCSV($filename);
        $lines = $csvFile->parse(4);

        if (is_null($lines)) {
            return 'noneInterval';
        }

        $locations = [];

        foreach($lines as $line) {
            $data = [
                'area' => (string)$line[0],
                'aisle' => (string)$line[1],
                'col' => (string)$line[2],
                'level' => (string)$line[3]                        
            ];            

            $location = new Location();

            foreach($data as $k=>$v) {
                if (preg_match('/^[a-z]$/', $v)) {
                    $data[$k] = strtoupper($v);
                }
                if (preg_match('/^[0-9]{1,3}$/', $v)) {
                    $data[$k] = $location->intToString((int)$v);
                }
                if ($v === '') {
                    return 'noneInterval';
                }
            }      

            $location->hydrate($data);
            $location->setConcatenate();
            $locations[] = $location;
        }

        $res = $this->repository->createLocations($locations);

        if ($res === count($locations)) {
            return "fullInterval";
        }

        if ($res === 0) {
            return "noneInterval";
        }

        return "partialInterval";
    }

    public function findAllLocations(?string $queryString = null, ?int $page = null): ?array
    {
        return $this->repository->findWhereAllPaginated(['concatenate', 'like', "%$queryString%"], $page);
    }

    public function delete(int $id): bool
    {
        return $this->repository->deleteWhere(['id', '=', $id]);
    }

    public function suggestLocations($concatenate): string
    {
        $limit = 5;
        $entities = $this->repository->findWhereAll(['concatenate', 'like', "%$concatenate%"], $limit);

        if (is_null($entities)) {
            return 'no-result';
        }

        $results = [];

        foreach($entities as $entity) {
            $results[] = $entity->getConcatenate();
        }

        return join(';', $results);
    }

    public function locationExists(string $location): bool
    {
        return !(is_null($this->repository->findWhere(['concatenate', '=', $location])));
    }
}
