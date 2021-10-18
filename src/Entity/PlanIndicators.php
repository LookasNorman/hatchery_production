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

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hatchersNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hatchersCapacity;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $settersNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $settersCapacity;

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

    public function getHatchersNumber(): ?int
    {
        return $this->hatchersNumber;
    }

    public function setHatchersNumber(?int $hatchersNumber): self
    {
        $this->hatchersNumber = $hatchersNumber;

        return $this;
    }

    public function getHatchersCapacity(): ?int
    {
        return $this->hatchersCapacity;
    }

    public function setHatchersCapacity(?int $hatchersCapacity): self
    {
        $this->hatchersCapacity = $hatchersCapacity;

        return $this;
    }

    public function getSettersNumber(): ?int
    {
        return $this->settersNumber;
    }

    public function setSettersNumber(?int $settersNumber): self
    {
        $this->settersNumber = $settersNumber;

        return $this;
    }

    public function getSettersCapacity(): ?int
    {
        return $this->settersCapacity;
    }

    public function setSettersCapacity(?int $settersCapacity): self
    {
        $this->settersCapacity = $settersCapacity;

        return $this;
    }
}
