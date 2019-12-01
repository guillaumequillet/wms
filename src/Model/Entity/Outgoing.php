<?php
declare(strict_types=1);

namespace App\Model\Entity;

class Outgoing extends Movement
{
    private $recipient;
    private $address;
    private $zipcode;
    private $city;
    private $country;

    public function __construct()
    {
        parent::__construct();
    }

    public function checkTypes(): void
    {
        if (!is_null($this->recipient)) {
            $this->setRecipient((string) $this->recipient);        
        }
        if (!is_null($this->address)) {
            $this->setAddress((string) $this->address);        
        }
        if (!is_null($this->zipcode)) {
            $this->setZipcode((string) $this->zipcode);        
        }
        if (!is_null($this->city)) {
            $this->setCity((string) $this->city);        
        }
        if (!is_null($this->country)) {
            $this->setCountry((string) $this->country);        
        }
        parent::checkTypes();
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getZipcode(): string
    {
        return $this->zipcode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setRecipient(string $recipient): self
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;
        return $this;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }
}
