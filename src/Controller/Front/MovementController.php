<?php
declare(strict_types=1);

namespace App\Controller\Front;

use App\Model\Manager\MovementManager;
use App\Controller\Controller;

class MovementController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->manager = new MovementManager();
    }

    public function index(): void 
    {
        $template = 'movement/index.twig.html';
        $data = ['token' => $this->token->generateString()];
        $this->render($template, $data);
    }

    public function incoming(): void
    {
        $template = 'movement/incoming.twig.html';
        $data = ['token' => $this->token->generateString()];
        $this->render($template, $data);
    }
}
