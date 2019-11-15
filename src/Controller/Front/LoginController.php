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
        // if login was successful
        $loggedIn = $this->manager->checkLogin($this->token);
        
        if ($loggedIn === "ok") {
            header('location: /');
            exit();
        }

        if ($loggedIn === "user" || $loggedIn === "pwd") {
            $this->setLog("0");
        }

        if ($loggedIn === "token") {
            $this->setLog("1");
        }

        $template = 'login.twig.html';
        $data = ['token' => $this->token->generateString()];       
        $this->render($template, $data);        
    }

    public function logout(): void
    {
        $this->superglobalManager->unsetVariable('session', 'username');
        $this->superglobalManager->unsetVariable('session', 'role');        
        $this->setLog("2");
        header('location: /');
    }
}
