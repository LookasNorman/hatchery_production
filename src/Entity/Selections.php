<?php

namespace App\Entity;

use App\Repository\SelectionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SelectionsRepository::class)
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
     * @ORM\OneToMany(targetEntity=InputsFarmDelivery::class, mappedBy="selections")
     */
    private $inputsFarmDelivery;

    public function __construct()
    {
        $this->inputsFarmDelivery = new ArrayCollection();
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
     * @return Collection|InputsFarmDelivery[]
     */
    public function getInputsFarmDelivery(): Collection
    {
        return $this->inputsFarmDelivery;
    }

    public function addInputsFarmDelivery(InputsFarmDelivery $inputsFarmDelivery): self
    {
        if (!$this->inputsFarmDelivery->contains($inputsFarmDelivery)) {
            $this->inputsFarmDelivery[] = $inputsFarmDelivery;
            $inputsFarmDelivery->setSelections($this);
        }

        return $this;
    }

    public function removeInputsFarmDelivery(InputsFarmDelivery $inputsFarmDelivery): self
    {
        if ($this->inputsFarmDelivery->removeElement($inputsFarmDelivery)) {
            // set the owning side to null (unless already changed)
            if ($inputsFarmDelivery->getSelections() === $this) {
                $inputsFarmDelivery->setSelections(null);
            }
        }

        return $this;
    }
}
