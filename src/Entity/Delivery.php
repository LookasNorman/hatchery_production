<?php

namespace App\Entity;

use App\Repository\DeliveryRepository;
use DateTimeInterface;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DeliveryRepository::class)
 * @Auditable()
 */
class Delivery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $deliveryDate;

    /**
     * @ORM\ManyToOne(targetEntity=Herds::class, inversedBy="eggsDeliveries")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull()
     */
    private $herd;

    /**
     * @ORM\Column(type="integer")
     */
    private $eggsNumber;

    /**
     * @ORM\Column(type="date")
     */
    private $firstLayingDate;

    /**
     * @ORM\Column(type="date")
     */
    private $lastLayingDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $eggsOnWarehouse;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     * @Assert\Length(
     *     allowEmptyString=false,
     *     min=6,
     *     max=6,
     *     minMessage="eggs_delivery.part_index.min",
     *     maxMessage="eggs_delivery.part_index.max"
     * )
     */
    private $partIndex;

    /**
     * @ORM\OneToMany(targetEntity=InputsFarmDeliveryPlan::class, mappedBy="delivery")
     */
    private $inputsFarmDeliveryPlans;

    /**
     * @ORM\OneToMany(targetEntity=InputDelivery::class, mappedBy="delivery")
     */
    private $inputDeliveries;

    /**
     * @ORM\OneToMany(targetEntity=SellingEgg::class, mappedBy="delivery")
     */
    private $sellingEggs;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $wasteLighting;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $fertilization;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $wasteEggsLighting;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lightingEggs;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $transfersEgg;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $chickNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cullChicken;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $unhatched;

    public function __construct()
    {
        $this->inputsFarmDeliveryPlans = new ArrayCollection();
        $this->inputDeliveries = new ArrayCollection();
        $this->sellingEggs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeliveryDate(): ?DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

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

    public function getEggsNumber(): ?int
    {
        return $this->eggsNumber;
    }

    public function setEggsNumber(int $eggsNumber): self
    {
        $this->eggsNumber = $eggsNumber;

        return $this;
    }

    public function getFirstLayingDate(): ?DateTimeInterface
    {
        return $this->firstLayingDate;
    }

    public function setFirstLayingDate(DateTimeInterface $firstLayingDate): self
    {
        $this->firstLayingDate = $firstLayingDate;

        return $this;
    }

    public function getLastLayingDate(): ?DateTimeInterface
    {
        return $this->lastLayingDate;
    }

    public function setLastLayingDate(DateTimeInterface $lastLayingDate): self
    {
        $this->lastLayingDate = $lastLayingDate;

        return $this;
    }

    public function getEggsOnWarehouse(): ?int
    {
        return $this->eggsOnWarehouse;
    }

    public function setEggsOnWarehouse(?int $eggsOnWarehouse): self
    {
        $this->eggsOnWarehouse = $eggsOnWarehouse;

        return $this;
    }

    public function getPartIndex(): ?string
    {
        return $this->partIndex;
    }

    public function setPartIndex(?string $partIndex): self
    {
        $this->partIndex = $partIndex;

        return $this;
    }

    /**
     * @return Collection|InputsFarmDeliveryPlan[]
     */
    public function getInputsFarmDeliveryPlans(): Collection
    {
        return $this->inputsFarmDeliveryPlans;
    }

    public function addInputsFarmDeliveryPlan(InputsFarmDeliveryPlan $inputsFarmDeliveryPlan): self
    {
        if (!$this->inputsFarmDeliveryPlans->contains($inputsFarmDeliveryPlan)) {
            $this->inputsFarmDeliveryPlans[] = $inputsFarmDeliveryPlan;
            $inputsFarmDeliveryPlan->setDelivery($this);
        }

        return $this;
    }

    public function removeInputsFarmDeliveryPlan(InputsFarmDeliveryPlan $inputsFarmDeliveryPlan): self
    {
        if ($this->inputsFarmDeliveryPlans->removeElement($inputsFarmDeliveryPlan)) {
            // set the owning side to null (unless already changed)
            if ($inputsFarmDeliveryPlan->getDelivery() === $this) {
                $inputsFarmDeliveryPlan->setDelivery(null);
            }
        }

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
            $inputDelivery->setDelivery($this);
        }

        return $this;
    }

    public function removeInputDelivery(InputDelivery $inputDelivery): self
    {
        if ($this->inputDeliveries->removeElement($inputDelivery)) {
            // set the owning side to null (unless already changed)
            if ($inputDelivery->getDelivery() === $this) {
                $inputDelivery->setDelivery(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SellingEgg[]
     */
    public function getSellingEggs(): Collection
    {
        return $this->sellingEggs;
    }

    public function addSellingEgg(SellingEgg $sellingEgg): self
    {
        if (!$this->sellingEggs->contains($sellingEgg)) {
            $this->sellingEggs[] = $sellingEgg;
            $sellingEgg->setDelivery($this);
        }

        return $this;
    }

    public function removeSellingEgg(SellingEgg $sellingEgg): self
    {
        if ($this->sellingEggs->removeElement($sellingEgg)) {
            // set the owning side to null (unless already changed)
            if ($sellingEgg->getDelivery() === $this) {
                $sellingEgg->setDelivery(null);
            }
        }

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

    public function addWasteLighting(?int $addWasteLighting): self
    {
        $this->wasteLighting = $this->wasteLighting + $addWasteLighting;

        return $this;
    }

    public function oddWasteLighting(?int $oddWasteLighting): self
    {
        $this->wasteLighting = $this->wasteLighting - $oddWasteLighting;

        return $this;
    }

    public function getFertilization(): ?string
    {
        return $this->fertilization;
    }

    public function setFertilization(?string $fertilization): self
    {
        $this->fertilization = $fertilization;

        return $this;
    }

    public function getWasteEggsLighting(): ?int
    {
        return $this->wasteEggsLighting;
    }

    public function setWasteEggsLighting(?int $wasteEggsLighting): self
    {
        $this->wasteEggsLighting = $wasteEggsLighting;

        return $this;
    }

    public function addWasteEggLighting(?int $addWasteEggLighting): self
    {
        $this->wasteEggsLighting = $this->wasteEggsLighting + $addWasteEggLighting;

        return $this;
    }

    public function oddWasteEggLighting(?int $oddWasteEggLighting): self
    {
        $this->wasteEggsLighting = $this->wasteEggsLighting - $oddWasteEggLighting;

        return $this;
    }

    public function getLightingEggs(): ?int
    {
        return $this->lightingEggs;
    }

    public function setLightingEggs(?int $lightingEggs): self
    {
        $this->lightingEggs = $lightingEggs;

        return $this;
    }

    public function addLightingEggs(?int $addLightingEggs): self
    {
        $this->lightingEggs = $this->lightingEggs + $addLightingEggs;

        return $this;
    }

    public function oddLightingEggs(?int $oddLightingEggs): self
    {
        $this->lightingEggs = $this->lightingEggs - $oddLightingEggs;

        return $this;
    }

    public function getTransfersEgg(): ?int
    {
        return $this->transfersEgg;
    }

    public function setTransfersEgg(?int $transfersEgg): self
    {
        $this->transfersEgg = $transfersEgg;

        return $this;
    }

    public function getChickNumber(): ?int
    {
        return $this->chickNumber;
    }

    public function setChickNumber(?int $chickNumber): self
    {
        $this->chickNumber = $chickNumber;

        return $this;
    }

    public function getCullChicken(): ?int
    {
        return $this->cullChicken;
    }

    public function setCullChicken(?int $cullChicken): self
    {
        $this->cullChicken = $cullChicken;

        return $this;
    }

    public function getUnhatched(): ?int
    {
        return $this->unhatched;
    }

    public function setUnhatched(?int $unhatched): self
    {
        $this->unhatched = $unhatched;

        return $this;
    }
}
