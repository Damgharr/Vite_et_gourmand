<?php

declare(strict_types=1);

namespace App\Document;

use App\Repository\FinishedOrderRepository;
use DateTimeImmutable;
use Doctrine\ODM\MongoDB\Mapping\Attribute as ODM;
use MongoDB\BSON\Decimal128;

#[ODM\Document(repositoryClass: FinishedOrderRepository::class)]
#[ODM\Index(keys: ['orderId' => 'asc'], unique: true)]
#[ODM\Index(keys: ['finishedAt' => 'asc'])]
class FinishedOrder
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'int')]
    private ?int $orderId = null;

    #[ODM\Field(type: 'string')]
    private ?string $orderNumber = null;

    #[ODM\Field(type: 'date_immutable')]
    private ?DateTimeImmutable $orderedAt = null;

    #[ODM\Field(type: 'date_immutable')]
    private ?DateTimeImmutable $finishedAt = null;

    #[ODM\Field(type: 'decimal128')]
    private ?Decimal128 $menuPrice = null;

    #[ODM\Field(type: 'decimal128')]
    private ?Decimal128 $deliveryPrice = null;

    #[ODM\Field(type: 'decimal128')]
    private ?Decimal128 $totalPrice = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): static
    {
        $this->orderId = $orderId;
        return $this;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): static
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getOrderedAt(): ?DateTimeImmutable
    {
        return $this->orderedAt;
    }

    public function setOrderedAt(DateTimeImmutable $orderedAt): static
    {
        $this->orderedAt = $orderedAt;
        return $this;
    }

    public function getFinishedAt(): ?DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(DateTimeImmutable $finishedAt): static
    {
        $this->finishedAt = $finishedAt;
        return $this;
    }

    public function getMenuPrice(): ?Decimal128
    {
        return $this->menuPrice;
    }

    public function setMenuPrice(Decimal128|string $menuPrice): static
    {
        $this->menuPrice = $menuPrice instanceof Decimal128 ? $menuPrice : new Decimal128((string) $menuPrice);
        return $this;
    }

    public function getDeliveryPrice(): ?Decimal128
    {
        return $this->deliveryPrice;
    }

    public function setDeliveryPrice(Decimal128|string $deliveryPrice): static
       {
        $this->deliveryPrice = $deliveryPrice instanceof Decimal128 ? $deliveryPrice : new Decimal128((string) $deliveryPrice);
        return $this;
    }

    public function getTotalPrice(): ?Decimal128
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(Decimal128|string $totalPrice): static
    {
        $this->totalPrice = $totalPrice instanceof Decimal128 ? $totalPrice : new Decimal128((string) $totalPrice);
        return $this;
    }
}
