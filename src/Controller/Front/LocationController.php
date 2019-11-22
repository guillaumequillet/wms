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
        $concatenate = $this->superglobalManager->findVariable('post', 'concatenate');

        if (is_null($concatenate)) {
            echo 'no-result';
            exit();
        }

        echo $this->manager->suggestLocations($concatenate);        
    }
}
