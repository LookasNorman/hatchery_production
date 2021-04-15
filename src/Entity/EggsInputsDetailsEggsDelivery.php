<?php

namespace App\Entity;

use App\Repository\EggsInputsDetailsEggsDeliveryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EggsInputsDetailsEggsDeliveryRepository::class)
 */
class EggsInputsDetailsEggsDelivery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=EggsInputsDetails::class, inversedBy="eggsInputsDetailsEggsDeliveries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eggsInputDetails;

    /**
     * @ORM\ManyToOne(targetEntity=EggsDelivery::class, inversedBy="eggsInputsDetailsEggsDeliveries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $EggsDeliveries;

    /**
     * @ORM\Column(type="integer")
     */
    private $eggsNumber;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEggsInputDetails(): ?EggsInputsDetails
    {
        return $this->eggsInputDetails;
    }

    public function setEggsInputDetails(?EggsInputsDetails $eggsInputDetails): self
    {
        $this->eggsInputDetails = $eggsInputDetails;

        return $this;
    }

    public function getEggsDeliveries(): ?EggsDelivery
    {
        return $this->EggsDeliveries;
    }

    public function setEggsDeliveries(?EggsDelivery $EggsDeliveries): self
    {
        $this->EggsDeliveries = $EggsDeliveries;

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
