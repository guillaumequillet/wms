<?php
declare(strict_types=1);

namespace App\Model\Entity;

class User extends Entity
{
    /* private attributes */
    private $id;
    private $username;
    private $password;
    private $email;    
    private $role;

    public function checkTypes(): void
    {
        $this->setId((int) $this->id);        
        $this->setUsername((string) $this->username);        
        $this->setPassword((string) $this->password);
        $this->setEmail((string) $this->email);        
        $this->setRole((string) $this->role);        
    }

    /* public getters */
    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    /* public setters */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }
}
