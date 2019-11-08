<?php
declare(strict_types=1);

namespace App\Model\Entity;

class Entity
{
    public function hydrate(array $data): self
    {
        foreach($data as $k => $v) {
            if (method_exists($this, 'set' . ucfirst($k))) {
                $method = 'set' . ucfirst($k);
                $this->$method($v);
            }
        }
        return $this;
    }
}
