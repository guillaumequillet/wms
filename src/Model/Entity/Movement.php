<?php
declare(strict_types=1);

namespace App\Model\Entity;

class Movement extends Entity
{
    /* private attributes */
    private $id;
    private $lines;
    private $createdAt;
    private $reference;
    private $status;
    private $user;

    public function checkTypes(): void
    {

    }

    /* public getters */
    public function getId(): int
    {
        return $this->id;
    }

    public function getLines(): array
    {
        return $this->lines;
    }

    public function getCreatedAt(): Datetime
    {
        return $this->createdAt;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getUser(): User
    {
        return $this->user;
    }
    /* public setters */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setLines(array $lines): self
    {
        $this->lines = $lines;
        return $this;
    }

    public function setCreatedAt(\DateTime $date): self
    {
        $this->createdAt = $date;
        return $this;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
