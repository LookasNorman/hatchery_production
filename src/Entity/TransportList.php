<?php

namespace App\Entity;

use App\Repository\TransportListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransportListRepository::class)
 */
class TransportList
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=InputsFarm::class, inversedBy="transportLists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $farm;

    /**
     * @ORM\ManyToMany(targetEntity=Driver::class, inversedBy="transportLists")
     */
    private $driver;

    /**
     * @ORM\ManyToOne(targetEntity=Car::class, inversedBy="transportLists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $car;

    /**
     * @ORM\Column(type="integer")
     */
    private $distance;

    /**
     * @ORM\Column(type="time")
     */
    private $departureHour;

    /**
     * @ORM\Column(type="time")
     */
    private $arrivalHourToFarm;

    public function __construct()
    {
        $this->driver = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFarm(): ?InputsFarm
    {
        return $this->farm;
    }

    public function setFarm(?InputsFarm $farm): self
    {
        $this->farm = $farm;

        return $this;
    }

    /**
     * @return Collection|Driver[]
     */
    public function getDriver(): Collection
    {
        return $this->driver;
    }

    public function addDriver(Driver $driver): self
    {
        if (!$this->driver->contains($driver)) {
            $this->driver[] = $driver;
        }

        return $this;
    }

    public function removeDriver(Driver $driver): self
    {
        $this->driver->removeElement($driver);

        return $this;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }

    public function setCar(?Car $car): self
    {
        $this->car = $car;

        return $this;
    }

    public function getDistance(): ?int
    {
        return $this->distance;
    }

    public function setDistance(int $distance): self
    {
        $this->distance = $distance;

        return $this;
    }

    public function getDepartureHour(): ?\DateTimeInterface
    {
        return $this->departureHour;
    }

    public function setDepartureHour(\DateTimeInterface $departureHour): self
    {
        $this->departureHour = $departureHour;

        return $this;
    }

    public function getArrivalHourToFarm(): ?\DateTimeInterface
    {
        return $this->arrivalHourToFarm;
    }

    public function setArrivalHourToFarm(\DateTimeInterface $arrivalHourToFarm): self
    {
        $this->arrivalHourToFarm = $arrivalHourToFarm;

        return $this;
    }
}