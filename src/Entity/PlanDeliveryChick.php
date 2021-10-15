<?php

namespace App\Entity;

use App\Repository\PlanDeliveryChickRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlanDeliveryChickRepository::class)
 */
class PlanDeliveryChick
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ChicksRecipient::class, inversedBy="planDeliveryChicks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chickFarm;

    /**
     * @ORM\Column(type="integer")
     */
    private $chickNumber;

    /**
     * @ORM\Column(type="datetime")
     */
    private $deliveryDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $inputDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lightingDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $transferDate;

    /**
     * @ORM\ManyToMany(targetEntity=Breed::class, inversedBy="planDeliveryChicks")
     * @ORM\JoinTable("plans_breeds")
     */
    private $breed;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChickFarm(): ?ChicksRecipient
    {
        return $this->chickFarm;
    }

    public function setChickFarm(?ChicksRecipient $chickFarm): self
    {
        $this->chickFarm = $chickFarm;

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

    public function getDeliveryDate(): ?\DateTimeInterface
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate(\DateTimeInterface $deliveryDate): self
    {
        $this->deliveryDate = $deliveryDate;

        return $this;
    }

    public function getInputDate(): ?\DateTimeInterface
    {
        return $this->inputDate;
    }

    public function setInputDate(\DateTimeInterface $inputDate): self
    {
        $this->inputDate = $inputDate;

        return $this;
    }

    public function getLightingDate(): ?\DateTimeInterface
    {
        return $this->lightingDate;
    }

    public function setLightingDate(\DateTimeInterface $lightingDate): self
    {
        $this->lightingDate = $lightingDate;

        return $this;
    }

    public function getTransferDate(): ?\DateTimeInterface
    {
        return $this->transferDate;
    }

    public function setTransferDate(\DateTimeInterface $transferDate): self
    {
        $this->transferDate = $transferDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getBreed()
    {
        return $this->breed;
    }

    /**
     * @param mixed $breed
     */
    public function setBreed($breed): void
    {
        $this->breed = $breed;
    }

}
