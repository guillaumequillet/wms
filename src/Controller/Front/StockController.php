<?php
declare(strict_types=1);

namespace App\Controller\Front;

use App\Model\Manager\StockManager;
use App\Controller\Controller;

class StockController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->manager = new StockManager();
    }

    public function index(int $page = 0): void
    {
        // we reset the queryString if new access from menu
        if ($page === 0) {
            $page = 1;
            $this->superglobalManager->unsetVariable('session', 'queryString');
        }

        $postQueryString = $this->superglobalManager->findVariable('post', 'queryString');
        
        if (!is_null($postQueryString) && $this->token->check()) {
            $this->superglobalManager->setVariable('session', 'queryString', $postQueryString);
        }

        $queryString = $this->superglobalManager->findVariable('session', 'queryString');

        if (!is_null($queryString) && $queryString === '') {
            $queryString = null;
        }

        $template = 'stock/index.twig.html';
        $data = ['token' => $this->token->generateString()];

        if (!is_null($queryString)) {
            $data['queryString'] = $queryString;
        }

        $stocks = $this->manager->findAllStocks($queryString, $page);

        foreach (['entities', 'currentPage', 'previousPage', 'nextPage'] as $key) {
            if (isset($stocks[$key]) && !is_null($stocks[$key])) {
                $data[$key] = $stocks[$key];
            }
        }
        $this->render($template, $data);
    }
}