<?php
declare(strict_types=1);

namespace App\Controller\Front;

class LoginController extends \App\Controller\Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function connected(): bool
    {
        $res = $this->superglobalManager->findVariable('session', 'username');
        return (!is_null($res));
    }

    public function login(): void
    {
        $template = 'login.twig.html';
        $data = [];
        $this->render($template, $data);        
    }
}
