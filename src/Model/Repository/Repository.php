<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Tool\Database;

abstract class Repository
{
    protected $database;

    public function __construct() 
    {
        $this->database = new Database();
    }

    protected function paginate(array $entities, int $totalResults, int $currentPage, int $resultsPerPage): ?array
    {
        $totalPages = (int)ceil($totalResults / $resultsPerPage);
        $previousPage = ($currentPage > 1) ? $currentPage - 1 : null;
        $nextPage = ($currentPage < $totalPages) ? $currentPage + 1 : null;

        return [
            'entities' => $entities,
            'totalResults' => $totalResults,
            'currentPage' => $currentPage,
            'nextPage' => $nextPage,
            'previousPage' => $previousPage,
            'totalPages' => $totalPages
        ];
    }
}
