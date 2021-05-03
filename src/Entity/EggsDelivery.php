<?php

namespace App\Entity;

use App\Repository\EggsDeliveryRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EggsDeliveryRepository::class)
 */
class EggsDelivery
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
     * @ORM\OneToMany(targetEntity=EggsInputsDetailsEggsDelivery::class, mappedBy="EggsDeliveries")
     */
    private $eggsInputsDetailsEggsDeliveries;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $eggsOnWarehouse;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     * @Assert\Length(
     *     min=6,
     *     max=6,
     *     minMessage="eggs_delivery.part_index.min",
     *     maxMessage="eggs_delivery.part_index.max"
     * )
     */
    private $partIndex;

    public function __construct()
    {
        $this->eggsInputsDetailsEggsDeliveries = new ArrayCollection();
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

    /**
     * @return Collection|EggsInputsDetailsEggsDelivery[]
     */
    public function getEggsInputsDetailsEggsDeliveries(): Collection
    {
        return $this->eggsInputsDetailsEggsDeliveries;
    }

    public function addEggsInputsDetailsEggsDelivery(EggsInputsDetailsEggsDelivery $eggsInputsDetailsEggsDelivery): self
    {
        if (!$this->eggsInputsDetailsEggsDeliveries->contains($eggsInputsDetailsEggsDelivery)) {
            $this->eggsInputsDetailsEggsDeliveries[] = $eggsInputsDetailsEggsDelivery;
            $eggsInputsDetailsEggsDelivery->setEggsDeliveries($this);
        }

        return $this;
    }

    public function removeEggsInputsDetailsEggsDelivery(EggsInputsDetailsEggsDelivery $eggsInputsDetailsEggsDelivery): self
    {
        if ($this->eggsInputsDetailsEggsDeliveries->removeElement($eggsInputsDetailsEggsDelivery)) {
            // set the owning side to null (unless already changed)
            if ($eggsInputsDetailsEggsDelivery->getEggsDeliveries() === $this) {
                $eggsInputsDetailsEggsDelivery->setEggsDeliveries(null);
            }
        }

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

}
