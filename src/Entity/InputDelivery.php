<?php

namespace App\Entity;

use App\Repository\InputDeliveryRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InputDeliveryRepository::class)
 * @Auditable()
 */
class InputDelivery
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
    private $eggsNumber;

    /**
     * @ORM\ManyToOne(targetEntity=Inputs::class, inversedBy="inputDeliveries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $input;

    /**
     * @ORM\ManyToOne(targetEntity=Delivery::class, inversedBy="inputDeliveries")
     * @ORM\JoinColumn(nullable=false)
     */
    private $delivery;

    /**
     * @ORM\ManyToMany(targetEntity=Lighting::class, inversedBy="inputDeliveries")
     */
    private $lighting;

    /**
     * @ORM\ManyToMany(targetEntity=Selections::class, inversedBy="inputDeliveries")
     */
    private $selection;

    public function __construct()
    {
        $this->lighting = new ArrayCollection();
        $this->selection = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getInput(): ?Inputs
    {
        return $this->input;
    }

    public function setInput(?Inputs $input): self
    {
        $this->input = $input;

        return $this;
    }

    public function getDelivery(): ?Delivery
    {
        return $this->delivery;
    }

    public function setDelivery(?Delivery $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * @return Collection|Lighting[]
     */
    public function getLighting(): Collection
    {
        return $this->lighting;
    }

    public function addLighting(Lighting $lighting): self
    {
        if (!$this->lighting->contains($lighting)) {
            $this->lighting[] = $lighting;
        }

        return $this;
    }

    public function removeLighting(Lighting $lighting): self
    {
        $this->lighting->removeElement($lighting);

        return $this;
    }

    /**
     * @return Collection|Selections[]
     */
    public function getSelection(): Collection
    {
        return $this->selection;
    }

    public function addSelection(Selections $selection): self
    {
        if (!$this->selection->contains($selection)) {
            $this->selection[] = $selection;
        }

        return $this;
    }

    public function removeSelection(Selections $selection): self
    {
        $this->selection->removeElement($selection);

        return $this;
    }
}
