<?php
declare(strict_types=1);

namespace App\Model\Manager;

use App\Tool\SuperglobalManager;

abstract class Manager
{
    protected $repository;
    protected $superglobalManager;

    public function __construct()
    {
        $this->superglobalManager = new SuperglobalManager();
    }
}
