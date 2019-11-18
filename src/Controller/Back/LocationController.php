<?php
declare(strict_types=1);

namespace App\Controller\Back;

use App\Model\Manager\LocationManager;

class LocationController extends \App\Controller\Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->manager = new LocationManager();
    }

    public function index(): void
    {
        $template = 'admin/location/index.twig.html';
        $data = ['token' => $this->token->generateString()];
        $this->render($template, $data);        
    }

    public function createSingle(): void
    {
        if (!$this->token->check()) {
            $this->setLog("0");
            header('location: /location/index');
            exit();
        }

        $res = $this->manager->createSingleLocation();
        $this->setLog($res ? "1" : "0");
        header('location: /location/index');
    }

    public function createInterval(): void
    {
        if (!$this->token->check()) {
            $this->setLog("0");
            header('location: /location/index');
            exit();
        }

        $res = $this->manager->createIntervalLocations();
        $this->setLog($res ? "3" : "2");
        header('location: /location/index');
    }
}
