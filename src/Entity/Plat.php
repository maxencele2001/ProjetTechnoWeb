<?php

namespace App\Entity;

use App\Repository\PlatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlatRepository::class)
 * @ORM\Table(name="plat")
 */
class Plat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=Restaurant::class, inversedBy="plats")
     */
    private $restaurants;

    /**
     * @ORM\OneToMany(targetEntity=OrderQuantity::class, mappedBy="plats")
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getRestaurants(): ?Restaurant
    {
        return $this->restaurants;
    }

    public function setRestaurants(?Restaurant $restaurants): self
    {
        $this->restaurants = $restaurants;

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
            $orderQuantity->setPlats($this);
        }

        return $this;
    }

    public function removeOrderQuantity(OrderQuantity $orderQuantity): self
    {
        if ($this->orderQuantities->removeElement($orderQuantity)) {
            // set the owning side to null (unless already changed)
            if ($orderQuantity->getPlats() === $this) {
                $orderQuantity->setPlats(null);
            }
        }

        return $this;
    }
}
