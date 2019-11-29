<?php
declare(strict_types=1);

namespace App\Model\Entity;

class Movement extends Entity
{
    /* private attributes */
    private $id;
    private $rows;
    private $createdAt;
    private $reference;
    private $user;

    public function checkTypes(): void
    {
        if (!is_null($this->id)) {
            $this->setId((int) $this->id);
        }
        if (!is_null($this->rows)) {
            $this->setRows($this->rows);    
        }        
        if (!is_null($this->createdAt)) {
            $this->setCreatedAt($this->createdAt);
        }    
        if (!is_null($this->reference)) {
            $this->setReference((string)$this->reference);     
        }
        if (!is_null($this->user)) {
            $this->setUser($this->user);    
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
}
