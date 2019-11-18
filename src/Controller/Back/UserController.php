<?php
declare(strict_types=1);

namespace App\Controller\Back;

use App\Model\Manager\UserManager;

class UserController extends \App\Controller\Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->manager = new UserManager();
    }

    public function index(): void
    {
        $template = 'admin/user.twig.html';
        $data = [];

        $users = $this->manager->getUsersList();

        if (!is_null($users)) {
            $data['users'] = $users;
        }

        $this->render($template, $data);        
    }
}
