<?php

namespace App\Entity;

use App\Repository\LightingRepository;
use DateTimeInterface;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LightingRepository::class)
 * @Auditable()
 */
class Lighting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull()
     */
    private $wasteEggs;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date()
     */
    private $lightingDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $lightingEggs;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $wasteLighting;

    /**
     * @ORM\ManyToMany(targetEntity=InputDelivery::class, mappedBy="lighting")
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

    public function getWasteEggs(): ?int
    {
        return $this->wasteEggs;
    }

    public function setWasteEggs(int $wasteEggs): self
    {
        $this->wasteEggs = $wasteEggs;

        return $this;
    }

    public function getLightingDate(): ?DateTimeInterface
    {
        return $this->lightingDate;
    }

    public function setLightingDate(DateTimeInterface $lightingDate): self
    {
        $this->lightingDate = $lightingDate;

        return $this;
    }

    public function getLightingEggs(): ?int
    {
        return $this->lightingEggs;
    }

    public function setLightingEggs(int $lightingEggs): self
    {
        $this->lightingEggs = $lightingEggs;

        return $this;
    }

    public function getWasteLighting(): ?int
    {
        return $this->wasteLighting;
    }

    public function setWasteLighting(?int $wasteLighting): self
    {
        $this->wasteLighting = $wasteLighting;

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
            $inputDelivery->addLighting($this);
        }

        return $this;
    }

    public function removeInputDelivery(InputDelivery $inputDelivery): self
    {
        if ($this->inputDeliveries->removeElement($inputDelivery)) {
            $inputDelivery->removeLighting($this);
        }

        return $this;
    }
}
