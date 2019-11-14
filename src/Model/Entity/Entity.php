<?php
declare(strict_types=1);

namespace App\Model\Entity;

abstract class Entity
{
    public function __construct()
    {
        $this->hydrate();
    }

    abstract public function hydrate(): void;
}
