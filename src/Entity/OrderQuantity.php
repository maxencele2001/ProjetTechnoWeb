<?php

namespace App\Entity;

use App\Repository\OrderQuantityRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderQuantityRepository::class)
 */
class OrderQuantity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Order::class, inversedBy="orderQuantities")
     */
    private $orders;

    /**
     * @ORM\ManyToOne(targetEntity=Plat::class, inversedBy="orderQuantities")
     */
    private $plats;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrders(): ?Order
    {
        return $this->orders;
    }

    public function setOrders(?Order $orders): self
    {
        $this->orders = $orders;

        return $this;
    }

    public function getPlats(): ?Plat
    {
        return $this->plats;
    }

    public function setPlats(?Plat $plats): self
    {
        $this->plats = $plats;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
