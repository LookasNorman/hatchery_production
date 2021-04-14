<?php

namespace App\Entity;

use App\Repository\EggsInputsDetailsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
     */
    private $eggInput;

    /**
     * @ORM\ManyToMany(targetEntity=EggsDelivery::class, inversedBy="eggsInputsDetails")
     */
    private $eggDelivery;

    public function __construct()
    {
        $this->eggDelivery = new ArrayCollection();
    }

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

    /**
     * @return Collection|EggsDelivery[]
     */
    public function getEggDelivery(): Collection
    {
        return $this->eggDelivery;
    }

    public function addEggDelivery(EggsDelivery $eggDelivery): self
    {
        if (!$this->eggDelivery->contains($eggDelivery)) {
            $this->eggDelivery[] = $eggDelivery;
        }

        return $this;
    }

    public function removeEggDelivery(EggsDelivery $eggDelivery): self
    {
        $this->eggDelivery->removeElement($eggDelivery);

        return $this;
    }
}
