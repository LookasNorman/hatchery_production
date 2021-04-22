<?php

namespace App\Entity;

use App\Repository\EggsSelectionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EggsSelectionsRepository::class)
 */
class EggsSelections
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=EggsInputsDetails::class, inversedBy="eggsSelections")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eggsInputsDetail;

    /**
     * @ORM\Column(type="integer")
     */
    private $chickNumber;

    /**
     * @ORM\Column(type="integer")
     */
    private $cullChicken;

    /**
     * @ORM\Column(type="date")
     */
    private $selectionDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEggsInputsDetail(): ?EggsInputsDetails
    {
        return $this->eggsInputsDetail;
    }

    public function setEggsInputsDetail(?EggsInputsDetails $eggsInputsDetail): self
    {
        $this->eggsInputsDetail = $eggsInputsDetail;

        return $this;
    }

    public function getChickNumber(): ?int
    {
        return $this->chickNumber;
    }

    public function setChickNumber(int $chickNumber): self
    {
        $this->chickNumber = $chickNumber;

        return $this;
    }

    public function getCullChicken(): ?int
    {
        return $this->cullChicken;
    }

    public function setCullChicken(int $cullChicken): self
    {
        $this->cullChicken = $cullChicken;

        return $this;
    }

    public function getSelectionDate(): ?\DateTimeInterface
    {
        return $this->selectionDate;
    }

    public function setSelectionDate(\DateTimeInterface $selectionDate): self
    {
        $this->selectionDate = $selectionDate;

        return $this;
    }
}
