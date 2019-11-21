<?php
declare(strict_types=1);

namespace App\Model\Entity;

class Location extends Entity
{
    /* private attributes */
    private $id;
    private $area;
    private $aisle;
    private $col;    
    private $level;
    private $concatenate;
    private $separator = "-";

    public function checkTypes(): void
    {
        $this->setId((int) $this->id);        
        $this->setArea((string) $this->area);        
        $this->setAisle((string) $this->aisle);
        $this->setCol((string) $this->col);        
        $this->setLevel((string) $this->level);        
    }

    public function intToString(int $number): string 
    {
        $size = strlen((string)$number);
        $output = '';
        
        for ($i = 1; $i <=  (3 - $size); $i++) {
            $output .= "0";
        }
        return $output . (string)$number;
    }
    
    public function setConcatenate(): void
    {
        $this->concatenate = $this->area . $this->separator . $this->aisle .  $this->separator . $this->col . $this->separator . $this->level;
    }

    /* public getters */
    public function getId(): int
    {
        return $this->id;
    }

    public function getArea(): string
    {
        return $this->area;
    }

    public function getAisle(): string
    {
        return $this->aisle;
    }

    public function getCol(): string
    {
        return $this->col;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function getConcatenate(): string
    {
        return $this->concatenate;
    }

    /* public setters */

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }
    
    public function setArea(string $area): self
    {
        $this->area = $area;
        return $this;
    }

    public function setAisle(string $aisle): self
    {
        $this->aisle = $aisle;
        return $this;
    }

    public function setCol(string $col): self
    {
        $this->col = $col;
        return $this;
    }

    public function setLevel(string $level): self
    {
        $this->level = $level;
        return $this;
    }
}
