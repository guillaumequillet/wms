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
        $this->setLog($res ? "okSingle" : "errorSingle");
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
        $this->setLog($res);
        header('location: /location/index');
    }

    public function import(): void
    {
        if ($this->token->check() === false) {
            $this->setLog('0');
            header('location: /location/index');
            exit();
        }

        $res = $this->manager->createLocations();
        $this->setLog($res);
        header('location: /location/index');       
    }
}
