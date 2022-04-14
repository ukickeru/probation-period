<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StockRepository::class)
 */
class Stock
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private string $symbol;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private string $currency;

    /**
     * @ORM\Column(type="integer")
     */
    private int $price;

    /**
     * @ORM\Column(type="string", length=50, nullable=false)
     */
    private string $name;

    public function __construct(
        string $symbol,
        string $currency,
        int $price,
        string $name
    ) {
        if (0 === strlen($symbol) || strlen($symbol) > 4) {
            throw new \DomainException('Stock symbol should be presented by non-empty string, no longer than 4 symbols!');
        }
        $this->symbol = strtoupper($symbol);

        if (0 === strlen($currency) || strlen($currency) > 3) {
            throw new \DomainException('Stock currency should be presented by non-empty string, no longer than 3 symbols!');
        }
        $this->currency = $currency;

        if ($price < 1) {
            throw new \DomainException('Can not set price lower than 1!');
        }
        $this->price = $price;

        if (0 === strlen($name) || strlen($name) > 30) {
            throw new \DomainException('Stock name should be presented by non-empty string, no longer than 50 symbols!');
        }
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): self
    {
        $this->symbol = $symbol;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price / 100;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
