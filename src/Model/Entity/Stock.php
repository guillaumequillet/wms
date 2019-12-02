<?php
declare(strict_types=1);

namespace App\Model\Entity;

class Stock extends Entity
{
    /* private attributes */
    private $id;
    private $location;
    private $article;
    private $qty;    
    private $reserved;

    public function checkTypes(): void
    {
        if (!is_null($this->id)) {
            $this->setId((int)$this->id);        
        }
        if (!is_null($this->qty)) {
            $this->setQty((int)$this->qty);        
        }
        if (!is_null($this->reserved)) {
            $this->setReserved((int)$this->reserved);        
        }
    }

    /* getters of initial SQL values */
    public function getArticleId(): ?int
    {
        if (is_int($this->article)) {
            return $this->article;
        }
        return null;
    }
    public function getLocationId(): ?int
    {
        if (is_int($this->location)) {
            return $this->location;
        }
        return null;
    }

    /* public getters */
    public function getId(): int
    {
        return $this->id;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getArticle(): Article
    {
        return $this->article;
    }

    public function getQty(): int
    {
        return $this->qty;
    }

    public function getReserved(): int
    {
        return $this->reserved;
    }

    /* public setters */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setLocation(Location $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function setArticle(Article $article): self
    {
        $this->article = $article;
        return $this;
    }

    public function setQty(int $qty): self
    {
        $this->qty = $qty;
        return $this;
    }

    public function setReserved(int $reserved): self
    {
        $this->reserved = $reserved;
        return $this;
    }
}
