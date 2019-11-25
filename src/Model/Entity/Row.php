<?php
declare(strict_types=1);

namespace App\Model\Entity;

class Row extends Entity
{
    private $id;
    private $movement;
    private $article;
    private $location;
    private $qty;

    public function checkTypes(): void
    {
        if (!is_null($this->id)) {
            $this->setId((int) $this->id);        
        }
        if (!is_null($this->movement)) {
            $this->setMovement((int) $this->movement);        
        }
        if (!is_null($this->article)) {
            $this->setArticle($this->article);
        }
        if (!is_null($this->location)) {
            $this->setLocation($this->location);        
        }
        if (!is_null($this->qty)) {
            $this->setQty((int) $this->qty);        
        }
    }

    /* public getters */
    public function getId(): int
    {
        return $this->id;
    }

    public function getMovement(): Movement
    {
        return $this->movement;
    }

    public function getArticle(): Article
    {
        return $this->article;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getQty(): int
    {
        return $this->qty;
    }

    /* public setters */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setMovement(Movement $movement): self
    {
        $this->movement = $movement;
        return $this;
    }

    public function setArticle(Article $article): self
    {
        $this->article = $article;
        return $this;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function setQty(int $qty): self
    {
        $this->qty = $qty;
        return $this;
    }
}
