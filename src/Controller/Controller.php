<?php
declare(strict_types=1);

namespace App\Controller;

use App\View\View;
use App\Tool\Database;

class Controller 
{
    protected $view;
    protected $entityManager;
    
    public function __construct()
    {
        $this->view = new View();
    }

    public function getView(): View
    {
        return $this->view;
    }
}
