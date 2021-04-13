<?php

namespace App\Entity;

use App\Repository\EggsDeliveryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EggsDeliveryRepository::class)
 */
class EggsDelivery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $deliveryDate;

    /**
     * @ORM\ManyToOne(targetEntity=Herds::class, inversedBy="eggsDeliveries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $herd;

    /**
     * @ORM\Column(type="integer")
     */
    private $eggsNumber;

    /**
     * @ORM\Column(type="date")
     */
    private $firstLayingDate;

    /**
     * @ORM\Column(type="date")
     */
    private $lastLayingDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getHerd(): ?Herds
    {
        return $this->herd;
    }

    public function setHerd(?Herds $herd): self
    {
        $this->herd = $herd;

        return $this;
    }

    public function getEggsNumber(): ?int
    {
        return $this->eggsNumber;
    }

    public function setEggsNumber(int $eggsNumber): self
    {
        $this->eggsNumber = $eggsNumber;

        return $this;
    }

    public function getFirstLayingDate(): ?\DateTimeInterface
    {
        return $this->firstLayingDate;
    }

    public function setFirstLayingDate(\DateTimeInterface $firstLayingDate): self
    {
        $this->firstLayingDate = $firstLayingDate;

        return $this;
    }

    public function getLastLayingDate(): ?\DateTimeInterface
    {
        return $this->lastLayingDate;
    }

    public function setLastLayingDate(\DateTimeInterface $lastLayingDate): self
    {
        $this->lastLayingDate = $lastLayingDate;

        return $this;
    }
}
