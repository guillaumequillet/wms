<?php
declare(strict_types=1);

namespace App\Controller;

use App\View\View;
use App\Tool\SuperglobalManager;
use App\Tool\Token;


abstract class Controller 
{
    protected $manager;
    protected $view;
    protected $token;
    protected $superglobalManager;
    
    public function __construct()
    {
        $this->view = new View();
        $this->superglobalManager = new SuperglobalManager();
        $this->token = new Token();
    }

    protected function getView(): View
    {
        return $this->view;
    }

    protected function setLog(string $message): void 
    {
        $this->superglobalManager->setVariable('session', 'log', $message);
    }

    protected function render(string $template, ?array $data = null): void
    {
        if (is_null($data)) {
            $data = [];
        }
        
        $log = $this->superglobalManager->findVariable('session', 'log');
        $role = $this->superglobalManager->findVariable('session', 'role');

        if (!is_null($log)) {
            $data['log'] = $log;
            $this->superglobalManager->unsetVariable('session', 'log');
        }

        if (!is_null($role)) {
            $data['role'] = $role;
        }

        $this->getView()->render($template, $data);
    }
}
