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

    public function index(int $page = 0): void 
    {
        // we reset the queryString if new access from menu
        if ($page === 0) {
            $page = 1;
            $this->superglobalManager->unsetVariable('session', 'queryString');
        }

        $postQueryString = $this->superglobalManager->findVariable('post', 'queryString');
        
        if (!is_null($postQueryString) && $this->token->check(3)) {
            $this->superglobalManager->setVariable('session', 'queryString', $postQueryString);
        }

        $queryString = $this->superglobalManager->findVariable('session', 'queryString');

        if (!is_null($queryString) && $queryString === '') {
            $queryString = null;
        }

        $template = 'admin/location/index.twig.html';
        $data = [
            'token0' => $this->token->generateString(0),
            'token1' => $this->token->generateString(1),
            'token2' => $this->token->generateString(2),
            'token3' => $this->token->generateString(3)
        ];

        if (!is_null($queryString)) {
            $data['queryString'] = $queryString;
        }

        $locations = $this->manager->findAllLocations($queryString, $page);

        foreach (['entities', 'currentPage', 'previousPage', 'nextPage'] as $key) {
            if (isset($locations[$key]) && !is_null($locations[$key])) {
                $data[$key] = $locations[$key];
            }
        }

        $this->render($template, $data);        
    }

    public function createSingle(): void
    {
        if (!$this->token->check(0)) {
            $this->setLog("errorSingle");
            header('location: /location/index');
            exit();
        }

        $res = $this->manager->createSingleLocation();
        $this->setLog($res ? "okSingle" : "errorSingle");
        header('location: /location/index');
    }

    public function createInterval(): void
    {
        if (!$this->token->check(1)) {
            $this->setLog("noneInterval");
            header('location: /location/index');
            exit();
        }

        $res = $this->manager->createIntervalLocations();
        $this->setLog($res);
        header('location: /location/index');
    }

    public function import(): void
    {
        if ($this->token->check(2) === false) {
            $this->setLog('noneInterval');
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
