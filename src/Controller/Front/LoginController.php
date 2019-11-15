<?php
declare(strict_types=1);

namespace App\Controller\Front;

use App\Model\Manager\UserManager;

class LoginController extends \App\Controller\Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->manager = new UserManager();
    }

    public function connected(): bool
    {
        $res = $this->superglobalManager->findVariable('session', 'username');
        return (!is_null($res));
    }

    public function login(): void
    {
        $template = 'login.twig.html';
        $data = ['token' => $this->token->generateString()];       

        if ($this->manager->checkLogin()) {

        }

        $this->render($template, $data);        
    }
}
