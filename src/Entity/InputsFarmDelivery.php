<?php

namespace App\Entity;

use App\Repository\InputsFarmDeliveryRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=InputsFarmDeliveryRepository::class)
 * @Auditable()
 */
class InputsFarmDelivery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=InputsFarm::class, inversedBy="inputsFarmDeliveries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $inputsFarm;

    /**
     * @ORM\ManyToOne(targetEntity=Delivery::class, inversedBy="inputsFarmDeliveries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $delivery;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull(message="inputs_farm_delivery.eggs_number")
     */
    private $eggsNumber;

    /**
     * @ORM\ManyToOne(targetEntity=Lighting::class, inversedBy="inputsFarmDelivery")
     * @ORM\JoinColumn(nullable=true)
     */
    private $lighting;

    /**
     * @ORM\ManyToOne(targetEntity=Transfers::class, inversedBy="inputsFarmDelivery")
     */
    private $transfers;

    /**
     * @ORM\ManyToOne(targetEntity=Selections::class, inversedBy="inputsFarmDelivery")
     */
    private $selections;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInputsFarm(): ?InputsFarm
    {
        return $this->inputsFarm;
    }

    public function setInputsFarm(?InputsFarm $inputsFarm): self
    {
        $this->inputsFarm = $inputsFarm;

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

    public function getEggsNumber(): ?int
    {
        return $this->eggsNumber;
    }

    public function setEggsNumber(int $eggsNumber): self
    {
        $this->eggsNumber = $eggsNumber;

        return $this;
    }

    public function getLighting(): ?Lighting
    {
        return $this->lighting;
    }

    public function setLighting(?Lighting $lighting): self
    {
        $this->lighting = $lighting;

        return $this;
    }

    public function getTransfers(): ?Transfers
    {
        return $this->transfers;
    }

    public function setTransfers(?Transfers $transfers): self
    {
        $this->transfers = $transfers;

        return $this;
    }

    public function getSelections(): ?Selections
    {
        return $this->selections;
    }

    public function setSelections(?Selections $selections): self
    {
        $this->selections = $selections;

        return $this;
    }
}
