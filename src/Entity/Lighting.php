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
     * @ORM\OneToMany(targetEntity=InputsFarmDelivery::class, mappedBy="lighting")
     */
    private $inputsFarmDelivery;

    /**
     * @ORM\Column(type="integer")
     */
    private $lightingEggs;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $wasteLighting;

    public function __construct()
    {
        $this->inputsFarmDelivery = new ArrayCollection();
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
            $inputsFarmDelivery->setLighting($this);
        }

        return $this;
    }

    public function removeInputsFarmDelivery(InputsFarmDelivery $inputsFarmDelivery): self
    {
        if ($this->inputsFarmDelivery->removeElement($inputsFarmDelivery)) {
            // set the owning side to null (unless already changed)
            if ($inputsFarmDelivery->getLighting() === $this) {
                $inputsFarmDelivery->setLighting(null);
            }
        }

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
}
