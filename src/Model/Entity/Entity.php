<?php
declare(strict_types=1);

namespace App\Model\Entity;

abstract class Entity
{
    public function __construct()
    {
        $this->checkTypes();
    }

    protected function hydrate(array $data): self
    {
        foreach($data as $key => $v) {
            $method = 'set' . ucfirst($key);
            if (method_exists(get_class($this), $method)) {
                $this->$method($v);
            }
        }
        return $this;
    }

    abstract public function checkTypes(): void;
}
