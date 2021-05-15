<?php

namespace App\Entity;

use App\Repository\EggsInputsDetailsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EggsInputsDetailsRepository::class)
 */
class InputsDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Inputs::class, inversedBy="eggsInputsDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eggInput;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull()
     */
    private $chickNumber;

    /**
     * @ORM\OneToMany(targetEntity=DetailsDelivery::class, mappedBy="eggsInputDetails")
     */
    private $eggsInputsDetailsEggsDeliveries;

    /**
     * @ORM\ManyToOne(targetEntity=ChicksRecipient::class, inversedBy="eggsInputsDetails")
     * @Assert\NotNull()
     */
    private $chicksRecipient;

    /**
     * @ORM\OneToMany(targetEntity=Lighting::class, mappedBy="eggsInputsDetail")
     */
    private $eggsInputsLightings;

    /**
     * @ORM\OneToMany(targetEntity=Transfers::class, mappedBy="eggsInputsDetail")
     */
    private $eggsInputsTransfers;

    /**
     * @ORM\OneToMany(targetEntity=Selections::class, mappedBy="eggsInputsDetail")
     */
    private $eggsSelections;

    public function __construct()
    {
        $this->eggsInputsDetailsEggsDeliveries = new ArrayCollection();
        $this->eggsInputsLightings = new ArrayCollection();
        $this->eggsInputsTransfers = new ArrayCollection();
        $this->eggsSelections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEggInput(): ?Inputs
    {
        return $this->eggInput;
    }

    public function setEggInput(?Inputs $eggInput): self
    {
        $this->eggInput = $eggInput;

        return $this;
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

    /**
     * @return Collection|DetailsDelivery[]
     */
    public function getEggsInputsDetailsEggsDeliveries(): Collection
    {
        return $this->eggsInputsDetailsEggsDeliveries;
    }

    public function addEggsInputsDetailsEggsDelivery(DetailsDelivery $eggsInputsDetailsEggsDelivery): self
    {
        if (!$this->eggsInputsDetailsEggsDeliveries->contains($eggsInputsDetailsEggsDelivery)) {
            $this->eggsInputsDetailsEggsDeliveries[] = $eggsInputsDetailsEggsDelivery;
            $eggsInputsDetailsEggsDelivery->setEggsInputDetails($this);
        }

        return $this;
    }

    public function removeEggsInputsDetailsEggsDelivery(DetailsDelivery $eggsInputsDetailsEggsDelivery): self
    {
        if ($this->eggsInputsDetailsEggsDeliveries->removeElement($eggsInputsDetailsEggsDelivery)) {
            // set the owning side to null (unless already changed)
            if ($eggsInputsDetailsEggsDelivery->getEggsInputDetails() === $this) {
                $eggsInputsDetailsEggsDelivery->setEggsInputDetails(null);
            }
        }

        return $this;
    }

    public function getChicksRecipient(): ?ChicksRecipient
    {
        return $this->chicksRecipient;
    }

    public function setChicksRecipient(?ChicksRecipient $chicksRecipient): self
    {
        $this->chicksRecipient = $chicksRecipient;

        return $this;
    }

    /**
     * @return Collection|Lighting[]
     */
    public function getEggsInputsLightings(): Collection
    {
        return $this->eggsInputsLightings;
    }

    public function addEggsInputsLighting(Lighting $eggsInputsLighting): self
    {
        if (!$this->eggsInputsLightings->contains($eggsInputsLighting)) {
            $this->eggsInputsLightings[] = $eggsInputsLighting;
            $eggsInputsLighting->setEggsInputsDetail($this);
        }

        return $this;
    }

    public function removeEggsInputsLighting(Lighting $eggsInputsLighting): self
    {
        if ($this->eggsInputsLightings->removeElement($eggsInputsLighting)) {
            // set the owning side to null (unless already changed)
            if ($eggsInputsLighting->getEggsInputsDetail() === $this) {
                $eggsInputsLighting->setEggsInputsDetail(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transfers[]
     */
    public function getEggsInputsTransfers(): Collection
    {
        return $this->eggsInputsTransfers;
    }

    public function addEggsInputsTransfer(Transfers $eggsInputsTransfer): self
    {
        if (!$this->eggsInputsTransfers->contains($eggsInputsTransfer)) {
            $this->eggsInputsTransfers[] = $eggsInputsTransfer;
            $eggsInputsTransfer->setEggsInputsDetail($this);
        }

        return $this;
    }

    public function removeEggsInputsTransfer(Transfers $eggsInputsTransfer): self
    {
        if ($this->eggsInputsTransfers->removeElement($eggsInputsTransfer)) {
            // set the owning side to null (unless already changed)
            if ($eggsInputsTransfer->getEggsInputsDetail() === $this) {
                $eggsInputsTransfer->setEggsInputsDetail(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Selections[]
     */
    public function getEggsSelections(): Collection
    {
        return $this->eggsSelections;
    }

    public function addEggsSelection(Selections $eggsSelection): self
    {
        if (!$this->eggsSelections->contains($eggsSelection)) {
            $this->eggsSelections[] = $eggsSelection;
            $eggsSelection->setEggsInputsDetail($this);
        }

        return $this;
    }

    public function removeEggsSelection(Selections $eggsSelection): self
    {
        if ($this->eggsSelections->removeElement($eggsSelection)) {
            // set the owning side to null (unless already changed)
            if ($eggsSelection->getEggsInputsDetail() === $this) {
                $eggsSelection->setEggsInputsDetail(null);
            }
        }

        return $this;
    }
}
