<?php

namespace App\Entity;

use App\Repository\PlanInputRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanInputRepository::class)
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
     * @ORM\ManyToMany(targetEntity=ChicksRecipient::class, inversedBy="planInputs")
     */
    private $farm;

    /**
     * @ORM\Column(type="integer")
     */
    private $chickNumber;

    /**
     * @ORM\ManyToMany(targetEntity=Herds::class, inversedBy="planInputs")
     */
    private $herd;

    /**
     * @ORM\Column(type="integer")
     */
    private $eggNumber;

    public function __construct()
    {
        $this->farm = new ArrayCollection();
        $this->herd = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|ChicksRecipient[]
     */
    public function getFarm(): Collection
    {
        return $this->farm;
    }

    public function addFarm(ChicksRecipient $farm): self
    {
        if (!$this->farm->contains($farm)) {
            $this->farm[] = $farm;
        }

        return $this;
    }

    public function removeFarm(ChicksRecipient $farm): self
    {
        $this->farm->removeElement($farm);

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

    /**
     * @return Collection|Herds[]
     */
    public function getHerd(): Collection
    {
        return $this->herd;
    }

    public function addHerd(Herds $herd): self
    {
        if (!$this->herd->contains($herd)) {
            $this->herd[] = $herd;
        }

        return $this;
    }

    public function removeHerd(Herds $herd): self
    {
        $this->herd->removeElement($herd);

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
}
