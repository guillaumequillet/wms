<?php
declare(strict_types=1);

namespace App\Controller\Front;

use App\Model\Manager\OutgoingManager;
use App\Controller\Controller;

class OutgoingController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->manager = new OutgoingManager();
    }

    public function available(): void
    {
        header('Content-type: application/json');

        $code = $this->superglobalManager->findVariable('post', 'code');
        $quantity = $this->superglobalManager->findVariable('post', 'quantity');

        if (is_null($code) || is_null($quantity)) {
            echo json_encode([]);
            exit();
        }

        $array = $this->manager->findAvailableStocks($code, (int)$quantity);
        echo json_encode($array);
    }

    public function confirm(): void
    {
        header('Content-type: application/json');

        if ($this->token->check(0, false) === false) {
            echo json_encode('tokenError');
            $this->setLog('tokenError');
            exit();
        }

        $log = $this->manager->createOutgoing() ? "moveOK" : "moveNOK";;
        $this->setLog($log);
        echo json_encode($log);
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

        $template = 'outgoing/index.twig.html';
        $data = ['token0' => $this->token->generateString(0)];

        if (!is_null($queryString)) {
            $data['queryString'] = $queryString;
        }

        $outgoings = $this->manager->findAllOutgoings($queryString, $page);

        foreach (['entities', 'currentPage', 'previousPage', 'nextPage'] as $key) {
            if (isset($outgoings[$key]) && !is_null($outgoings[$key])) {
                $data[$key] = $outgoings[$key];
            }
        }
        $this->render($template, $data);
    }

    public function edit(?int $id = null): void
    {
        $template = 'outgoing/edit.twig.html';
        $data = ['token0' => $this->token->generateString(0)];

        if (!is_null($id)) {
            $data['outgoing'] = $this->manager->getOutgoing($id);
        }

        if (!is_null($id) && is_null($data['outgoing'])) {
            $this->setLog('unfound');
            header('location: /outgoing/index');
            exit();
        }

        $this->render($template, $data);
    }

    public function delete(int $id): void
    {
        $res = $this->manager->deleteOutgoing($id);
        $this->setLog($res ? 'deleteOK' : 'deleteNOK');
        header('location: /outgoing/index');
    }

    public function unreserve(): void
    {
        header('Content-type: application/json');

        $code = $this->superglobalManager->findVariable('post', 'code');
        $orderId = $this->superglobalManager->findVariable('post', 'id');

        if (is_null($code) || is_null($orderId)) {
            echo json_encode(false);
            exit();
        }

        echo json_encode($this->manager->unreserve($code, (int)$orderId));
    }
}
