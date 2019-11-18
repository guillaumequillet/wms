<?php
declare(strict_types=1);

namespace App\Model\Manager;

use App\Model\Entity\Location;
use App\Model\Repository\LocationRepository;

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

        if (in_array(null, $data)) {
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

    public function createIntervalLocations(): bool
    {
        $fields = [
            'area' => $this->superglobalManager->findVariable("post", "area"),
            'fromAisle' => $this->superglobalManager->findVariable("post", "fromAisle"),
            'toAisle' => $this->superglobalManager->findVariable("post", "toAisle"),
            'fromCol' => $this->superglobalManager->findVariable("post", "fromCol"),
            'toCol' => $this->superglobalManager->findVariable("post", "toCol"),
            'fromLevel' => $this->superglobalManager->findVariable("post", "fromLevel"),
            'toLevel' => $this->superglobalManager->findVariable("post", "toLevel")
        ];

        if (in_array(null, $fields)) {
            return false;
        }

        $types = [];
        foreach ($fields as $k=>$v) {
            $types[$k] = is_numeric($v) ? "int" : "string";
        }

        if ($types['fromAisle'] !== $types['toAisle']) {
            return false;
        }

        if ($types['fromCol'] !== $types['toCol']) {
            return false;
        }

        if ($types['fromLevel'] !== $types['toLevel']) {
            return false;
        }

        $res = true;

        foreach (range($fields['fromAisle'], $fields['toAisle']) as $aisle) {
            foreach (range($fields['fromCol'], $fields['toCol']) as $col) {
                foreach (range($fields['fromLevel'], $fields['toLevel']) as $level) {
                    $location = new Location();
                    $location->hydrate([
                        'area' => (string)$fields['area'],
                        'aisle' => (string)$aisle,
                        'col' => (string)$col,
                        'level' => (string)$level                        
                    ]);
                    $location->setConcatenate();
                    $res = $this->repository->createLocation($location);
                }
            }
        }
        return $res;
    }
}
