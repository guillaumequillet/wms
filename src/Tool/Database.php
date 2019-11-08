<?php
declare(strict_types=1);

namespace App\Tool;
use \PDO;

class Database 
{
    private $pdo;
    private $host = 'localhost';
    private $database = 'projet5';
    private $username = 'root';
    private $password = '';

    public function getPDO(): PDO 
    {
        if (!isset($this->pdo)) {
            $this->pdo = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->database . ';charset=utf8', $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
        }       
        return $this->pdo;
    }
}
