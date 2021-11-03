<?php

namespace App\Entity;

use App\Repository\ChickTemperatureRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChickTemperatureRepository::class)
 * @Auditable()
 */
class ChickTemperature
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
    private $date;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=1)
     */
    private $temperature;

    /**
     * @ORM\ManyToOne(targetEntity=Inputs::class, inversedBy="chickTemperatures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $input;

    /**
     * @ORM\ManyToOne(targetEntity=Hatchers::class, inversedBy="chickTemperatures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $hatcher;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTemperature(): ?string
    {
        return $this->temperature;
    }

    public function setTemperature(string $temperature): self
    {
        $this->temperature = $temperature;

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

    public function getHatcher(): ?Hatchers
    {
        return $this->hatcher;
    }

    public function setHatcher(?Hatchers $hatcher): self
    {
        $this->hatcher = $hatcher;

        return $this;
    }
}
