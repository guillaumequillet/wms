<?php
declare(strict_types=1);

namespace App\Controller\Back;

use App\Model\Manager\LocationManager;
use App\Controller\Controller;

class LocationController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->manager = new LocationManager();
    }

    public function index(?int $page = null): void
    {
        $template = 'admin/location/index.twig.html';
        $data = ['token' => $this->token->generateString()];

        $locations = $this->manager->findLocations($page);

        foreach (['entities', 'currentPage', 'previousPage', 'nextPage'] as $key) {
            if (isset($locations[$key]) && !is_null($locations[$key])) {
                $data[$key] = $locations[$key];
            }
        }

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

    public function delete(int $id): void
    {
        $res = $this->manager->delete($id);
        $this->setLog($res ? "deleteOk" : "deleteFail");
        header('location: /location/index');
    }
}
