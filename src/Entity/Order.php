<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $ordered_at;

    /**
     * @ORM\Column(type="float")
     */
    private $priceTotal;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="orders")
     */
    private $restaurant;

    /**
     * @ORM\OneToMany(targetEntity=OrderQuantity::class, mappedBy="orders")
     */
    private $orderQuantities;

    public function __construct()
    {
        $this->orderQuantities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderedAt(): ?\DateTimeInterface
    {
        return $this->ordered_at;
    }

    public function setOrderedAt(\DateTimeInterface $ordered_at): self
    {
        $this->ordered_at = $ordered_at;

        return $this;
    }

    public function getPriceTotal(): ?float
    {
        return $this->priceTotal;
    }

    public function setPriceTotal(float $priceTotal): self
    {
        $this->priceTotal = $priceTotal;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): self
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    /**
     * @return Collection|OrderQuantity[]
     */
    public function getOrderQuantities(): Collection
    {
        return $this->orderQuantities;
    }

    public function addOrderQuantity(OrderQuantity $orderQuantity): self
    {
        if (!$this->orderQuantities->contains($orderQuantity)) {
            $this->orderQuantities[] = $orderQuantity;
            $orderQuantity->setOrders($this);
        }

        return $this;
    }

    public function removeOrderQuantity(OrderQuantity $orderQuantity): self
    {
        if ($this->orderQuantities->removeElement($orderQuantity)) {
            // set the owning side to null (unless already changed)
            if ($orderQuantity->getOrders() === $this) {
                $orderQuantity->setOrders(null);
            }
        }

        return $this;
    }
}
