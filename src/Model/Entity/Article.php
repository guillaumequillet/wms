<?php
declare(strict_types=1);

namespace App\Model\Entity;

class Article extends Entity
{
    /* private attributes */
    private $id;
    private $code;
    private $description;
    private $weight;    
    private $width;
    private $height;
    private $length;
    private $barcode;

    public function hydrate(): void
    {
        $this->setId((int) $this->id);        
        $this->setCode((string) $this->code);        
        $this->setDescription((string) $this->description);
        $this->setWeight((int) $this->weight);        
        $this->setWidth((int) $this->width);        
        $this->setHeight((int) $this->height);        
        $this->setLength((int) $this->length);        
        $this->setBarcode((string) $this->barcode);        
    }

    /* public getters */
    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getBarcode(): string
    {
        return $this->barcode;
    }

    /* public setters */
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setWeight(int $weight = 0): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function setWidth(int $width = 0): self
    {
        $this->width = $width;
        return $this;
    }

    public function setHeight(int $height = 0): self
    {
        $this->height = $height;
        return $this;
    }

    public function setLength(int $length = 0): self
    {
        $this->length = $length;
        return $this;
    }

    public function setBarcode(string $barcode): self
    {
        $this->barcode = $barcode;
        return $this;
    }    

    // protected setter
    protected function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
}
