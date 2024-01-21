<?php

namespace App\Entity;

use App\Repository\AddressQueryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressQueryRepository::class)]
class AddressQuery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $asset;

    #[ORM\Column(length: 255)]
    private string $address;

    #[ORM\Column(name: 'before_date', type: Types::DATE_MUTABLE)]
    private \DateTimeInterface $before;

    #[ORM\Column(name: 'after_date', type: Types::DATE_MUTABLE)]
    private \DateTimeInterface $after;

    #[ORM\Column]
    private int $threshold;

    #[ORM\Column]
    private int $transactionCount;

    #[ORM\Column]
    private float $averageTransactionQuantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAsset(): string
    {
        return $this->asset;
    }

    public function setAsset(string $asset): static
    {
        $this->asset = $asset;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getBefore(): \DateTimeInterface
    {
        return $this->before;
    }

    public function setBefore(\DateTimeInterface $before): static
    {
        $this->before = $before;

        return $this;
    }

    public function getAfter(): \DateTimeInterface
    {
        return $this->after;
    }

    public function setAfter(\DateTimeInterface $after): static
    {
        $this->after = $after;

        return $this;
    }

    public function getThreshold(): int
    {
        return $this->threshold;
    }

    public function setThreshold(int $threshold): static
    {
        $this->threshold = $threshold;

        return $this;
    }

    public function getTransactionCount(): int
    {
        return $this->transactionCount;
    }

    public function setTransactionCount(int $transactionCount): static
    {
        $this->transactionCount = $transactionCount;

        return $this;
    }

    public function getAverageTransactionQuantity(): float
    {
        return $this->averageTransactionQuantity;
    }

    public function setAverageTransactionQuantity(float $averageTransactionQuantity): static
    {
        $this->averageTransactionQuantity = $averageTransactionQuantity;

        return $this;
    }
}
