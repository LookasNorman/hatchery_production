<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=CarRepository::class)
 * @UniqueEntity("name", message="car.name.isset")
 * @UniqueEntity("registrationNumber", message="car.registration_number.isset")
 */
class Car
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
     */
    private $registrationNumber;

    /**
     * @ORM\OneToMany(targetEntity=TransportList::class, mappedBy="car")
     */
    private $transportLists;

    public function __construct()
    {
        $this->transportLists = new ArrayCollection();
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

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber(string $registrationNumber): self
    {
        $this->registrationNumber = $registrationNumber;

        return $this;
    }

    /**
     * @return Collection|TransportList[]
     */
    public function getTransportLists(): Collection
    {
        return $this->transportLists;
    }

    public function addTransportList(TransportList $transportList): self
    {
        if (!$this->transportLists->contains($transportList)) {
            $this->transportLists[] = $transportList;
            $transportList->setCar($this);
        }

        return $this;
    }

    public function removeTransportList(TransportList $transportList): self
    {
        if ($this->transportLists->removeElement($transportList)) {
            // set the owning side to null (unless already changed)
            if ($transportList->getCar() === $this) {
                $transportList->setCar(null);
            }
        }

        return $this;
    }
}
