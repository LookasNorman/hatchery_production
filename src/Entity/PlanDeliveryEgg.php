<?php

namespace App\Entity;

use App\Repository\PlanDeliveryEggRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanDeliveryEggRepository::class)
 */
class PlanDeliveryEgg
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Herds::class, inversedBy="planDeliveryEggs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $herd;

    /**
     * @ORM\Column(type="integer")
     */
    private $eggsNumber;

    /**
     * @ORM\Column(type="datetime")
     */
    private $deliveryDate;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }
}
