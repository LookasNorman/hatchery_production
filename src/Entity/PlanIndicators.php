<?php

namespace App\Entity;

use App\Repository\PlanIndicatorsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanIndicatorsRepository::class)
 */
class PlanIndicators
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
    private $fertilization;

    /**
     * @ORM\Column(type="integer")
     */
    private $transferHatchability;

    /**
     * @ORM\Column(type="integer")
     */
    private $hatchability;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFertilization(): ?int
    {
        return $this->fertilization;
    }

    public function setFertilization(int $fertilization): self
    {
        $this->fertilization = $fertilization;

        return $this;
    }

    public function getTransferHatchability(): ?int
    {
        return $this->transferHatchability;
    }

    public function setTransferHatchability(int $transferHatchability): self
    {
        $this->transferHatchability = $transferHatchability;

        return $this;
    }

    public function getHatchability(): ?int
    {
        return $this->hatchability;
    }

    public function setHatchability(int $hatchability): self
    {
        $this->hatchability = $hatchability;

        return $this;
    }
}
