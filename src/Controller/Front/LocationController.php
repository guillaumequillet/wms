<?php
declare(strict_types=1);

namespace App\Controller\Front;

use App\Model\Manager\LocationManager;
use App\Controller\Controller;

class LocationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->manager = new LocationManager();
    }

    public function suggestions(): void
    {
        header('Content-type: application/json');

        $concatenate = $this->superglobalManager->findVariable('post', 'concatenate');

        if (is_null($concatenate)) {
            echo json_encode([]);
            exit();
        }

        $array = $this->manager->suggestLocations($concatenate);
        echo json_encode($array);
    }

    public function locationExists(): void
    {
        header('Content-type: application/json');

        $location = $this->superglobalManager->findVariable('post', 'concatenate');

        if (is_null($location)) {
            echo json_encode(false);
            exit();
        }

        $exists = $this->manager->locationExists($location);
        echo json_encode($exists);
    }
}
