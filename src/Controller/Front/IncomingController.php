<?php
declare(strict_types=1);

namespace App\Controller\Front;

use App\Model\Manager\IncomingManager;
use App\Controller\Controller;

class IncomingController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->manager = new IncomingManager();
    }

    public function index(int $page = 0): void 
    {
        // we reset the queryString if new access from menu
        if ($page === 0) {
            $page = 1;
            $this->superglobalManager->unsetVariable('session', 'queryString');
        }

        $postQueryString = $this->superglobalManager->findVariable('post', 'queryString');

        if (!is_null($postQueryString) && $this->token->check(0)) {
            $this->superglobalManager->setVariable('session', 'queryString', $postQueryString);
        }

        $queryString = $this->superglobalManager->findVariable('session', 'queryString');

        if (!is_null($queryString) && $queryString === '') {
            $queryString = null;
        }

        $template = 'incoming/index.twig.html';
        $data = ['token0' => $this->token->generateString(0)];

        if (!is_null($queryString)) {
            $data['queryString'] = $queryString;
        }

        $incomings = $this->manager->findAllIncomings($queryString, $page);

        foreach (['entities', 'currentPage', 'previousPage', 'nextPage'] as $key) {
            if (isset($incomings[$key]) && !is_null($incomings[$key])) {
                $data[$key] = $incomings[$key];
            }
        }
        $this->render($template, $data);
    }

    public function edit(?int $id = null): void
    {
        $template = 'incoming/edit.twig.html';
        $data = ['token0' => $this->token->generateString(0)];

        if (!is_null($id)) {
            $data['incoming'] = $this->manager->getIncoming($id);
        }

        if (!is_null($id) && is_null($data['incoming'])) {
            $this->setLog('unfound');
            header('location: /incoming/index');
            exit();
        }

        $this->render($template, $data);
    }

    public function confirm(): void
    {
        if ($this->token->check(0) === false) {
            $this->setLog('tokenError');
            header('location: /incoming/index');
            exit();
        }

        $res = $this->manager->createIncoming();

        $this->setLog($res ? "moveOK" : "moveNOK");
        header('location: /incoming/index');
    }

    public function delete(int $id): void
    {
        $res = $this->manager->deleteIncoming($id);
        if (!$res) {
            $this->setLog('deleteNOK');
        }
        header('location: /incoming/index');
    }
}
