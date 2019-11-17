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
            echo "false";
            exit();
        }

        $res = $this->manager->createSingleLocation();

        $this->setLog($res ? "1" : "0");

        header('location: /location/index');
    }
}
