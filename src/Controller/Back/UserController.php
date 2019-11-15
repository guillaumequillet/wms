<?php
declare(strict_types=1);

namespace App\Controller\Back;

class UserController extends \App\Controller\Controller
{

    public function __construct()
    {
        parent::__construct();
        // $this->manager = new UserManager();
    }

    public function index(): void
    {
        $template = 'admin/user.twig.html';
        $data = [];
        $this->render($template, $data);        
    }
}
