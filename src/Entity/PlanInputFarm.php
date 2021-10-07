<?php

namespace App\Entity;

use App\Repository\PlanInputFarmRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanInputFarmRepository::class)
 */
class PlanInputFarm
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=PlanInput::class, inversedBy="planInputFarms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eggInput;

    /**
     * @ORM\Column(type="integer")
     */
    private $chickNumber;

    /**
     * @ORM\ManyToOne(targetEntity=ChicksRecipient::class, inversedBy="planInputFarms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chicksFarm;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEggInput(): ?PlanInput
    {
        return $this->eggInput;
    }

    public function setEggInput(?PlanInput $eggInput): self
    {
        $this->eggInput = $eggInput;

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

    public function getChicksFarm(): ?ChicksRecipient
    {
        return $this->chicksFarm;
    }

    public function setChicksFarm(?ChicksRecipient $chicksFarm): self
    {
        $this->chicksFarm = $chicksFarm;

        return $this;
    }
}
