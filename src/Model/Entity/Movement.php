<?php
declare(strict_types=1);

namespace App\Model\Entity;

class Movement extends Entity
{
    /* private attributes */
    protected $id;
    protected $rows;
    protected $createdAt;
    protected $reference;
    protected $user;
    public $userId;
    protected $status;

    public function checkTypes(): void
    {
        if (isset($this->id)) {
            $this->setId((int) $this->id);
        }
        if (isset($this->created_at)) {
            $this->setCreatedAt($this->created_at);
        }    
        if (isset($this->reference)) {
            $this->setReference((string)$this->reference);     
        }
        if (isset($this->status)) {
            $this->setStatus($this->status);    
        }
        if (isset($this->user)) {
            $this->userId = $this->user;
        }
    }

    /* public getters */
    public function getId(): int
    {
        return $this->id;
    }

    public function getRows(): array
    {
        return $this->rows;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /* public setters */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setRows(array $rows): self
    {
        $this->rows = $rows;
        return $this;
    }

    public function setCreatedAt(string $date): self
    {
        if (is_null($date)) {
            $date = (new \DateTime())->format('Y-m-d H:i:s');
        }
        $this->createdAt = $date;
        return $this;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
}
