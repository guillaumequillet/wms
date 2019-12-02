<?php
declare(strict_types=1);

namespace App\Model\Entity;

abstract class Entity
{
    public function __construct()
    {
        $this->checkTypes();
    }

    public function hydrate(array $data): self
    {
        foreach($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists(get_class($this), $method)) {
                $this->$method($value);
            }
        }
        return $this;
    }

    abstract public function checkTypes(): void;
}
