<?php

namespace App\Entity;

use App\Repository\TransportListRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TransportListRepository::class)
 * @Auditable()
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

    /**
     * @ORM\ManyToMany(targetEntity=InputsFarm::class, inversedBy="transportLists")
     * @Assert\Count(
     *     min=1
     * )
     */
    private $farm;

    public function __construct()
    {
        $this->driver = new ArrayCollection();
        $this->farm = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|InputsFarm[]
     */
    public function getFarm(): Collection
    {
        return $this->farm;
    }

    public function addFarm(InputsFarm $farm): self
    {
        if (!$this->farm->contains($farm)) {
            $this->farm[] = $farm;
        }

        return $this;
    }

    public function removeFarm(InputsFarm $farm): self
    {
        $this->farm->removeElement($farm);

        return $this;
    }
}
