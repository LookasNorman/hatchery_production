<?php

namespace App\Entity;

use App\Repository\EggsInputsDetailsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EggsInputsDetailsRepository::class)
 */
class EggsInputsDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=EggsInputs::class, inversedBy="eggsInputsDetails")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $eggInput;

    /**
     * @ORM\Column(type="integer")
     */
    private $eggsNumber;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEggInput(): ?EggsInputs
    {
        return $this->eggInput;
    }

    public function setEggInput(?EggsInputs $eggInput): self
    {
        $this->eggInput = $eggInput;

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
