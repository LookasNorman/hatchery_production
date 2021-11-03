<?php

namespace App\Entity;

use App\Repository\InputsFarmDeliveryPlanRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InputsFarmDeliveryPlanRepository::class)
 * @Auditable()
 */
class InputsFarmDeliveryPlan
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=InputsFarm::class, inversedBy="inputsFarmDeliveryPlans")
     * @ORM\JoinColumn(nullable=false)
     */
    private $inputsFarm;

    /**
     * @ORM\ManyToOne(targetEntity=Delivery::class, inversedBy="inputsFarmDeliveryPlans")
     * @ORM\JoinColumn(nullable=false)
     */
    private $delivery;

    /**
     * @ORM\Column(type="integer")
     */
    private $eggsNumber;

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
}
