<?php
declare(strict_types=1);

namespace App\Controller\Back;

use App\Model\Manager\UserManager;
use App\Controller\Controller;

class UserController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->manager = new UserManager();
    }

    public function index(): void
    {
        $template = 'admin/user.twig.html';
        $data = ['token' => $this->token->generateString()];

        $users = $this->manager->getUsersList();

        if (!is_null($users)) {
            $data['users'] = $users;
        }

        $this->render($template, $data);        
    }

    public function createSingle(): void
    {
        if (!$this->token->check()) {
            $this->setLog("0");
            header('location: /user/index');
            exit();
        }

        $res = $this->manager->createSingleUser();
        $this->setLog($res ? "1" : "0");

        header('location: /user/index');
    }

    public function updateSingle(): void
    {
        $res = $this->manager->updateSingleUser();
        $this->setLog($res ? "1" : "0");

        header('location: /user/index');
    }
}
