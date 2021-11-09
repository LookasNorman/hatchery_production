<?php

namespace App\Entity;

use App\Repository\InputDeliveryRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InputDeliveryRepository::class)
 * @Auditable()
 */
class InputDelivery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $eggsNumber;

    /**
     * @ORM\ManyToOne(targetEntity=Inputs::class, inversedBy="inputDeliveries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $input;

    /**
     * @ORM\ManyToOne(targetEntity=Delivery::class, inversedBy="inputDeliveries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $delivery;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getInput(): ?Inputs
    {
        return $this->input;
    }

    public function setInput(?Inputs $input): self
    {
        $this->input = $input;

        return $this;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(?Delivery $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }
}
