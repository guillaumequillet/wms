<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Tool\Database;
use App\Model\Entity\Entity;


abstract class Repository
{
    protected $database;

    public function __construct() 
    {
        $this->database = new Database();
    }

    protected function getTableName(): string
    {
        $repositoryClassname = get_class($this);
        $cut = explode('\\', $repositoryClassname);
        $repositoryName = $cut[array_key_last($cut)];
        $entityName = explode('Repository', $repositoryName)[0];
        return strtolower($entityName) . 's';        
    }

    protected function getEntityClassName(): string
    {
        $repositoryClassname = get_class($this);
        $cut = explode('\\', $repositoryClassname);
        $repositoryName = $cut[array_key_last($cut)];
        $entityName = explode('Repository', $repositoryName)[0];
        return "App\\Model\\Entity\\$entityName";
    }

    public function findWhere(array $conditions): ?Entity
    {
        $reqString = 'SELECT * FROM ' . $this->getTableName() . ' WHERE ';

        $conditionStrings = [];

        foreach ($conditions as $k => $v) {
            $conditionStrings[] = "$k = :$k";
        }

        $reqString .= join("AND", $conditionStrings);
        $reqString .= ' LIMIT 1';

        $stmt = $this->database->getPDO()->prepare($reqString);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, $this->getEntityClassName()); 
        $stmt->execute($conditions);
        $res = $stmt->fetch();
        return ($res === false) ? null : $res;        
    }

    public function findWhereAll(array $conditions = [], ?int $limit = null, ?int $offset = null): ?array
    {
        $reqString = 'SELECT * FROM ' . $this->getTableName();

        if (!empty($conditions)) {
            $reqString .= ' WHERE ';
            $conditionStrings = [];

            foreach ($conditions as $k => $v) {
                $conditionStrings[] = "$k = :$k";
            }
    
            $reqString .= join("AND", $conditionStrings);
        }

        if (!is_null($limit)) {
            $reqString .= ' LIMIT ' . $limit;
        }

        if (!is_null($offset)) {
            $reqString .= ' OFFSET ' . $offset;
        }

        $stmt = $this->database->getPDO()->prepare($reqString);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, $this->getEntityClassName()); 
        $stmt->execute($conditions);
        $res = $stmt->fetchAll();
        return ($res === false) ? null : $res;        
    }

    public function deleteWhere(array $conditions): bool
    {
        $reqString = 'DELETE FROM ' . $this->getTableName() . ' WHERE ';

        $conditionStrings = [];

        foreach ($conditions as $k => $v) {
            $conditionStrings[] = "$k = :$k";
        }

        $reqString .= join("AND", $conditionStrings);

        $stmt = $this->database->getPDO()->prepare($reqString);
        $res = $stmt->execute($conditions);
        return $res;
    }

    public function count(array $conditions = []): int
    {
        $reqString = 'SELECT COUNT(*) as total FROM ' . $this->getTableName();

        if (!empty($conditions)) {
            $reqString .= ' WHERE ';
            $conditionStrings = [];

            foreach ($conditions as $k => $v) {
                $conditionStrings[] = "$k = :$k";
            }
    
            $reqString .= join("AND", $conditionStrings);
        }

        $req = $this->database->getPDO()->prepare($reqString);
        $res = $req->execute($conditions);
        return ($res === false) ? 0 : $req->fetch()['total'];  
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
