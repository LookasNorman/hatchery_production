<?php

namespace App\Entity;

use App\Repository\PlanInputRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanInputRepository::class)
 * @Auditable()
 */
class PlanInput
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
    private $chickNumber;

    /**
     * @ORM\Column(type="integer")
     */
    private $eggNumber;

    /**
     * @ORM\ManyToOne(targetEntity=Inputs::class, inversedBy="planInputs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $input;

    /**
     * @ORM\ManyToOne(targetEntity=ChicksRecipient::class, inversedBy="planInputs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $farm;

    /**
     * @ORM\ManyToOne(targetEntity=Herds::class, inversedBy="planInputs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $herd;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEggNumber(): ?int
    {
        return $this->eggNumber;
    }

    public function setEggNumber(int $eggNumber): self
    {
        $this->eggNumber = $eggNumber;

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

    public function getFarm(): ?ChicksRecipient
    {
        return $this->farm;
    }

    public function setFarm(?ChicksRecipient $farm): self
    {
        $this->farm = $farm;

        return $this;
    }

    public function getHerd(): ?Herds
    {
        return $this->herd;
    }

    public function setHerd(?Herds $herd): self
    {
        $this->herd = $herd;

        return $this;
    }
}
