<?php

namespace App\Entity;

use App\Repository\EggsInputsDetailsEggsDeliveryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EggsInputsDetailsEggsDeliveryRepository::class)
 */
class DetailsDelivery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=InputsDetails::class, inversedBy="eggsInputsDetailsEggsDeliveries")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $eggsInputDetails;

    /**
     * @ORM\ManyToOne(targetEntity=Delivery::class, inversedBy="eggsInputsDetailsEggsDeliveries")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $eggsDeliveries;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull()
     */
    private $eggsNumber;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEggsInputDetails(): ?InputsDetails
    {
        return $this->eggsInputDetails;
    }

    public function setEggsInputDetails(?InputsDetails $eggsInputDetails): self
    {
        $this->eggsInputDetails = $eggsInputDetails;

        return $this;
    }

    public function getEggsDeliveries(): ?Delivery
    {
        return $this->eggsDeliveries;
    }

    public function setEggsDeliveries(?Delivery $eggsDeliveries): self
    {
        $this->eggsDeliveries = $eggsDeliveries;

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
