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
     * @ORM\Column(type="datetime")
     */
    private $inputDate;

    /**
     * @ORM\OneToMany(targetEntity=PlanInputFarm::class, mappedBy="eggInput")
     */
    private $planInputFarms;

    public function __construct()
    {
        $this->planInputFarms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInputDate(): ?\DateTimeInterface
    {
        return $this->inputDate;
    }

    public function setInputDate(\DateTimeInterface $inputDate): self
    {
        $this->inputDate = $inputDate->sub(new \DateInterval('P21DT5H'));

        return $this;
    }

    /**
     * @return Collection|PlanInputFarm[]
     */
    public function getPlanInputFarms(): Collection
    {
        return $this->planInputFarms;
    }

    public function addPlanInputFarm(PlanInputFarm $planInputFarm): self
    {
        if (!$this->planInputFarms->contains($planInputFarm)) {
            $this->planInputFarms[] = $planInputFarm;
            $planInputFarm->setEggInput($this);
        }

        return $this;
    }

    public function removePlanInputFarm(PlanInputFarm $planInputFarm): self
    {
        if ($this->planInputFarms->removeElement($planInputFarm)) {
            // set the owning side to null (unless already changed)
            if ($planInputFarm->getEggInput() === $this) {
                $planInputFarm->setEggInput(null);
            }
        }

        return $this;
    }
}
