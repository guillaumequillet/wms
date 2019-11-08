<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Tool\Database;

class Repository
{
    protected $database;

    public function __construct() 
    {
        $this->database = new Database();
    }
}
