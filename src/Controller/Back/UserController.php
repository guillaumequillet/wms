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
        $data = ['token0' => $this->token->generateString(0)];

        $users = $this->manager->getUsersList();

        if (!is_null($users)) {
            $data['users'] = $users;
        }

        $this->render($template, $data);        
    }

    public function create(): void
    {
        if (!$this->token->check(0)) {
            $this->setLog("0");
            header('location: /user/index');
            exit();
        }

        $res = $this->manager->createUser();
        $this->setLog($res ? "creationOk" : "creationFail");
        header('location: /user/index');
    }

    public function update(int $id): void
    {
        if (!$this->token->check(0)) {
            $this->setLog("updateFail");
            header('location: /user/show/' . $id);
            exit();
        }

        $res = $this->manager->updateUser($id);
        $this->setLog($res ? "updateOk" : "updateFail");
        header('location: /user/show/' . $id);        
    }

    public function delete(int $id): void
    {
        $res = $this->manager->deleteUser($id);
        $this->setLog($res ? "deleteOk" : "deleteFail");
        header('location: /user/index');
    }

    public function show(int $id): void
    {
        $template = 'admin/user/show.twig.html';
        $data = ['token0' => $this->token->generateString(0)];

        $user = $this->manager->getUser($id);

        if (!is_null($user)) {
            $data['user'] = $user;
        }

        $this->render($template, $data);        
    }
}
