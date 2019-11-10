<?php
declare(strict_types=1);

namespace App\Controller;

use App\View\View;
use App\Tool\SuperglobalManager;
use App\Tool\Token;


class Controller 
{
    protected $entityManager;
    protected $view;
    protected $token;
    protected $superglobalManager;
    
    public function __construct()
    {
        $this->view = new View();
        $this->superglobalManager = new SuperglobalManager();
        $this->token = new Token();
    }

    public function getView(): View
    {
        return $this->view;
    }
}
