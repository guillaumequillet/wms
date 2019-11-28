<?php
declare(strict_types=1);

namespace App\Controller\Front;

use App\Model\Manager\LoginManager;
use App\Controller\Controller;

class LoginController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->manager = new LoginManager();
    }

    public function connected(): bool
    {
        $res = $this->superglobalManager->findVariable('session', 'username');
        return (!is_null($res));
    }

    public function isAdmin(): bool
    {
        $res = $this->superglobalManager->findVariable('session', 'role');
        return ($res === 'admin' || $res === "superadmin");        
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

        $template = 'admin/user/login.twig.html';
        $data = ['token0' => $this->token->generateString(0)];       
        $this->render($template, $data);        
    }

    public function logout(): void
    {
        $this->superglobalManager->unsetVariable('session', 'username');
        $this->superglobalManager->unsetVariable('session', 'role');        
        $this->superglobalManager->unsetVariable('session', 'loggedIn');        
        $this->setLog("2");
        header('location: /');
    }
}
