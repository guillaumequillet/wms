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
        $template = 'admin/user/index.twig.html';
        $data = ['token' => $this->token->generateString()];

        $users = $this->manager->getUsersList();

        if (!is_null($users)) {
            $data['users'] = $users;
        }

        $this->render($template, $data);        
    }

    public function create(): void
    {
        if (!$this->token->check()) {
            $this->setLog("0");
            header('location: /user/index');
            exit();
        }

        $res = $this->manager->createUser();
        $this->setLog($res ? "creationOk" : "creationFail");
        header('location: /user/index');
    }

    public function delete(int $id): void
    {
        $res = $this->manager->deleteUser($id);
        $this->setLog($res ? "deleteOk" : "deleteFail");
        header('location: /user/index');
    }

    public function show(int $id): void
    {

    }
}
