<?php

namespace App\Entity;

use App\Repository\InputsFarmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InputsFarmRepository::class)
 */
class InputsFarm
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Inputs::class, inversedBy="inputsFarms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eggInput;

    /**
     * @ORM\Column(type="integer")
     */
    private $chickNumber;

    /**
     * @ORM\ManyToOne(targetEntity=ChicksRecipient::class, inversedBy="inputsFarms")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chicksFarm;

    /**
     * @ORM\OneToMany(targetEntity=InputsFarmDelivery::class, mappedBy="inputsFarm")
     */
    private $inputsFarmDeliveries;

    /**
     * @ORM\OneToMany(targetEntity=InputsFarmDeliveryPlan::class, mappedBy="inputsFarm")
     */
    private $inputsFarmDeliveryPlans;

    /**
     * @ORM\OneToMany(targetEntity=TransportList::class, mappedBy="farm")
     */
    private $transportLists;

    public function __construct()
    {
        $this->inputsFarmDeliveries = new ArrayCollection();
        $this->inputsFarmDeliveryPlans = new ArrayCollection();
        $this->transportLists = new ArrayCollection();
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

    public function getChicksFarm(): ?ChicksRecipient
    {
        return $this->chicksFarm;
    }

    public function setChicksFarm(?ChicksRecipient $chicksFarm): self
    {
        $this->chicksFarm = $chicksFarm;

        return $this;
    }

    /**
     * @return Collection|InputsFarmDelivery[]
     */
    public function getInputsFarmDeliveries(): Collection
    {
        return $this->inputsFarmDeliveries;
    }

    public function addInputsFarmDelivery(InputsFarmDelivery $inputsFarmDelivery): self
    {
        if (!$this->inputsFarmDeliveries->contains($inputsFarmDelivery)) {
            $this->inputsFarmDeliveries[] = $inputsFarmDelivery;
            $inputsFarmDelivery->setInputsFarm($this);
        }

        return $this;
    }

    public function removeInputsFarmDelivery(InputsFarmDelivery $inputsFarmDelivery): self
    {
        if ($this->inputsFarmDeliveries->removeElement($inputsFarmDelivery)) {
            // set the owning side to null (unless already changed)
            if ($inputsFarmDelivery->getInputsFarm() === $this) {
                $inputsFarmDelivery->setInputsFarm(null);
            }
        }

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
            $inputsFarmDeliveryPlan->setInputsFarm($this);
        }

        return $this;
    }

    public function removeInputsFarmDeliveryPlan(InputsFarmDeliveryPlan $inputsFarmDeliveryPlan): self
    {
        if ($this->inputsFarmDeliveryPlans->removeElement($inputsFarmDeliveryPlan)) {
            // set the owning side to null (unless already changed)
            if ($inputsFarmDeliveryPlan->getInputsFarm() === $this) {
                $inputsFarmDeliveryPlan->setInputsFarm(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TransportList[]
     */
    public function getTransportLists(): Collection
    {
        return $this->transportLists;
    }

    public function addTransportList(TransportList $transportList): self
    {
        if (!$this->transportLists->contains($transportList)) {
            $this->transportLists[] = $transportList;
            $transportList->setFarm($this);
        }

        return $this;
    }

    public function removeTransportList(TransportList $transportList): self
    {
        if ($this->transportLists->removeElement($transportList)) {
            // set the owning side to null (unless already changed)
            if ($transportList->getFarm() === $this) {
                $transportList->setFarm(null);
            }
        }

        return $this;
    }
}
