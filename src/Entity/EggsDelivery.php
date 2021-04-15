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
     * @ORM\ManyToMany(targetEntity=EggsInputsDetails::class, mappedBy="eggDelivery")
     */
    private $eggsInputsDetails;

    /**
     * @ORM\OneToMany(targetEntity=EggsInputsDetailsEggsDelivery::class, mappedBy="EggsDeliveries")
     */
    private $eggsInputsDetailsEggsDeliveries;

    public function __construct()
    {
        $this->eggsInputsDetails = new ArrayCollection();
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
     * @return Collection|EggsInputsDetails[]
     */
    public function getEggsInputsDetails(): Collection
    {
        return $this->eggsInputsDetails;
    }

    public function addEggsInputsDetail(EggsInputsDetails $eggsInputsDetail): self
    {
        if (!$this->eggsInputsDetails->contains($eggsInputsDetail)) {
            $this->eggsInputsDetails[] = $eggsInputsDetail;
            $eggsInputsDetail->addEggDelivery($this);
        }

        return $this;
    }

    public function removeEggsInputsDetail(EggsInputsDetails $eggsInputsDetail): self
    {
        if ($this->eggsInputsDetails->removeElement($eggsInputsDetail)) {
            $eggsInputsDetail->removeEggDelivery($this);
        }

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
}
