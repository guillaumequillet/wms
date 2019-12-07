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

    public function getOrderRows(): array
    {
        $rows = $this->getRows();
        $compactRows = [];

        foreach($rows as $row) {
            $article = $row->getArticle()->getCode();
            $location = $row->getLocation()->getConcatenate();
            $qty = $row->getQty();
            if (!isset($compactRows[$article])) {
                $compactRows[$article] = [
                    'totalQty' => 0,
                    'rows' => []
                ];
            }
            $compactRows[$article]['totalQty'] += $qty;
            $compactRows[$article]['rows'][] = [
                'location' => $location, 
                'qty' => $qty
            ];
        }

        return $compactRows;
    }

    public function getRecipient(): string
    {
        return html_entity_decode($this->recipient);
    }

    public function getAddress(): string
    {
        return html_entity_decode($this->address);
    }

    public function getZipcode(): string
    {
        return html_entity_decode($this->zipcode);
    }

    public function getCity(): string
    {
        return html_entity_decode($this->city);
    }

    public function getCountry(): string
    {
        return html_entity_decode($this->country);
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
