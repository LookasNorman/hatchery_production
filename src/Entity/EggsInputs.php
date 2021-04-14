<?php

namespace App\Entity;

use App\Repository\EggsInputsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EggsInputsRepository::class)
 */
class EggsInputs
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="date")
     */
    private $inputDate;

    /**
     * @ORM\Column(type="date")
     */
    private $lightingDate;

    /**
     * @ORM\Column(type="date")
     */
    private $transportDate;

    /**
     * @ORM\Column(type="date")
     */
    private $outputDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getInputDate(): ?\DateTimeInterface
    {
        return $this->inputDate;
    }

    public function setInputDate(\DateTimeInterface $inputDate): self
    {
        $this->inputDate = $inputDate;

        return $this;
    }

    public function getLightingDate(): ?\DateTimeInterface
    {
        return $this->lightingDate;
    }

    public function setLightingDate(\DateTimeInterface $lightingDate): self
    {
        $this->lightingDate = $lightingDate;

        return $this;
    }

    public function getTransportDate(): ?\DateTimeInterface
    {
        return $this->transportDate;
    }

    public function setTransportDate(\DateTimeInterface $transportDate): self
    {
        $this->transportDate = $transportDate;

        return $this;
    }

    public function getOutputDate(): ?\DateTimeInterface
    {
        return $this->outputDate;
    }

    public function setOutputDate(\DateTimeInterface $outputDate): self
    {
        $this->outputDate = $outputDate;

        return $this;
    }
}
