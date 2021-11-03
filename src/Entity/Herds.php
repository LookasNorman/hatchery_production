<?php

namespace App\Entity;

use App\Repository\HerdsRepository;
use DateTimeInterface;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=HerdsRepository::class)
 * @Auditable()
 */
class Herds
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(
     *     allowEmptyString=false,
     *     min = 3,
     *     max = 50,
     *     minMessage = "herds.name.min",
     *     maxMessage = "herds.name.max"
     * )
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Supplier::class, inversedBy="herds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $breeder;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date(
     * )
     */
    private $hatchingDate;

    /**
     * @ORM\ManyToOne(targetEntity=Breed::class, inversedBy="herds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $breed;

    /**
     * @ORM\OneToMany(targetEntity=Delivery::class, mappedBy="herd")
     */
    private $eggsDeliveries;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     */
    private $active;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lighting;

    /**
     * @ORM\OneToMany(targetEntity=PlanDeliveryEgg::class, mappedBy="herd", orphanRemoval=true)
     */
    private $planDeliveryEggs;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hensNumber;

    /**
     * @ORM\OneToMany(targetEntity=Vaccination::class, mappedBy="herd")
     */
    private $vaccinations;

    public function __construct()
    {
        $this->eggsDeliveries = new ArrayCollection();
        $this->deliveries = new ArrayCollection();
        $this->planDeliveryEggs = new ArrayCollection();
        $this->vaccinations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBreeder(): ?Supplier
    {
        return $this->breeder;
    }

    public function setBreeder(?Supplier $breeder): self
    {
        $this->breeder = $breeder;

        return $this;
    }

    public function getHatchingDate(): ?DateTimeInterface
    {
        return $this->hatchingDate;
    }

    public function setHatchingDate(DateTimeInterface $hatchingDate): self
    {
        $this->hatchingDate = $hatchingDate;

        return $this;
    }

    public function getBreed(): ?Breed
    {
        return $this->breed;
    }

    public function setBreed(?Breed $breed): self
    {
        $this->breed = $breed;

        return $this;
    }

    /**
     * @return Collection|Delivery[]
     */
    public function getEggsDeliveries(): Collection
    {
        return $this->eggsDeliveries;
    }

    public function addEggsDelivery(Delivery $eggsDelivery): self
    {
        if (!$this->eggsDeliveries->contains($eggsDelivery)) {
            $this->eggsDeliveries[] = $eggsDelivery;
            $eggsDelivery->setHerd($this);
        }

        return $this;
    }

    public function removeEggsDelivery(Delivery $eggsDelivery): self
    {
        if ($this->eggsDeliveries->removeElement($eggsDelivery)) {
            // set the owning side to null (unless already changed)
            if ($eggsDelivery->getHerd() === $this) {
                $eggsDelivery->setHerd(null);
            }
        }

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getLighting(): ?int
    {
        return $this->lighting;
    }

    public function setLighting(?int $lighting): self
    {
        $this->lighting = $lighting;

        return $this;
    }

    /**
     * @return Collection|PlanDeliveryEgg[]
     */
    public function getPlanDeliveryEggs(): Collection
    {
        return $this->planDeliveryEggs;
    }

    public function addPlanDeliveryEgg(PlanDeliveryEgg $planDeliveryEgg): self
    {
        if (!$this->planDeliveryEggs->contains($planDeliveryEgg)) {
            $this->planDeliveryEggs[] = $planDeliveryEgg;
            $planDeliveryEgg->setHerd($this);
        }

        return $this;
    }

    public function removePlanDeliveryEgg(PlanDeliveryEgg $planDeliveryEgg): self
    {
        if ($this->planDeliveryEggs->removeElement($planDeliveryEgg)) {
            // set the owning side to null (unless already changed)
            if ($planDeliveryEgg->getHerd() === $this) {
                $planDeliveryEgg->setHerd(null);
            }
        }

        return $this;
    }

    public function getHensNumber(): ?int
    {
        return $this->hensNumber;
    }

    public function setHensNumber(?int $hensNumber): self
    {
        $this->hensNumber = $hensNumber;

        return $this;
    }

    /**
     * @return Collection|Vaccination[]
     */
    public function getVaccinations(): Collection
    {
        return $this->vaccinations;
    }

    public function addVaccination(Vaccination $vaccination): self
    {
        if (!$this->vaccinations->contains($vaccination)) {
            $this->vaccinations[] = $vaccination;
            $vaccination->setHerd($this);
        }

        return $this;
    }

    public function removeVaccination(Vaccination $vaccination): self
    {
        if ($this->vaccinations->removeElement($vaccination)) {
            // set the owning side to null (unless already changed)
            if ($vaccination->getHerd() === $this) {
                $vaccination->setHerd(null);
            }
        }

        return $this;
    }
}
