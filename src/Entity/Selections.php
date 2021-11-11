<?php

namespace App\Entity;

use App\Repository\SelectionsRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SelectionsRepository::class)
 * @Auditable()
 */
class Selections
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
    private $cullChicken;

    /**
     * @ORM\Column(type="date")
     */
    private $selectionDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $unhatched;

    /**
     * @ORM\ManyToMany(targetEntity=InputDelivery::class, mappedBy="selection")
     */
    private $inputDeliveries;

    public function __construct()
    {
        $this->inputDeliveries = new ArrayCollection();
    }

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

    public function getCullChicken(): ?int
    {
        return $this->cullChicken;
    }

    public function setCullChicken(int $cullChicken): self
    {
        $this->cullChicken = $cullChicken;

        return $this;
    }

    public function getSelectionDate(): ?\DateTimeInterface
    {
        return $this->selectionDate;
    }

    public function setSelectionDate(\DateTimeInterface $selectionDate): self
    {
        $this->selectionDate = $selectionDate;

        return $this;
    }

    public function getUnhatched(): ?int
    {
        return $this->unhatched;
    }

    public function setUnhatched(int $unhatched): self
    {
        $this->unhatched = $unhatched;

        return $this;
    }

    /**
     * @return Collection|InputDelivery[]
     */
    public function getInputDeliveries(): Collection
    {
        return $this->inputDeliveries;
    }

    public function addInputDelivery(InputDelivery $inputDelivery): self
    {
        if (!$this->inputDeliveries->contains($inputDelivery)) {
            $this->inputDeliveries[] = $inputDelivery;
            $inputDelivery->addSelection($this);
        }

        return $this;
    }

    public function removeInputDelivery(InputDelivery $inputDelivery): self
    {
        if ($this->inputDeliveries->removeElement($inputDelivery)) {
            $inputDelivery->removeSelection($this);
        }

        return $this;
    }
}
