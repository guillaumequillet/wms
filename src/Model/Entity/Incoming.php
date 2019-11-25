<?php
declare(strict_types=1);

namespace App\Model\Entity;

class Incoming extends Movement
{
    private $provider;

    public function __construct()
    {
        parent::__construct();
    }

    public function checkTypes(): void
    {
        if (!is_null($this->provider)) {
            $this->setProvider((int) $this->provider);        
        }
        parent::checkTypes();
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): self
    {
        $this->provider = $provider;
        return $this;
    }
}
