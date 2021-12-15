<?php

namespace App\Entity;

use App\Repository\TransportInputsFarmRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TransportInputsFarmRepository::class)
 * @Auditable()
 */
class TransportInputsFarm
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TransportList::class, inversedBy="transportInputsFarms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $transportList;

    /**
     * @ORM\ManyToOne(targetEntity=InputsFarm::class, inversedBy="transportInputsFarms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $farm;

    /**
     * @ORM\Column(type="integer")
     */
    private $distanceFromHatchery;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $arrivalTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $distance;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTransportList(): ?TransportList
    {
        return $this->transportList;
    }

    public function setTransportList(?TransportList $transportList): self
    {
        $this->transportList = $transportList;

        return $this;
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

    public function getDistanceFromHatchery(): ?int
    {
        return $this->distanceFromHatchery;
    }

    public function setDistanceFromHatchery(int $distanceFromHatchery): self
    {
        $this->distanceFromHatchery = $distanceFromHatchery;

        return $this;
    }

    public function getArrivalTime(): ?\DateTimeInterface
    {
        return $this->arrivalTime;
    }

    public function setArrivalTime(?\DateTimeInterface $arrivalTime): self
    {
        $this->arrivalTime = $arrivalTime;

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
}
