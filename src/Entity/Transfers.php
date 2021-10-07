<?php

namespace App\Entity;

use App\Repository\TransfersRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TransfersRepository::class)
 */
class Transfers
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date()
     */
    private $transferDate;

    /**
     * @ORM\Column(type="integer")
     */
    private $transfersEgg;

    /**
     * @ORM\OneToMany(targetEntity=InputsFarmDelivery::class, mappedBy="transfers")
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
    
    public function getTransferDate(): ?DateTimeInterface
    {
        return $this->transferDate;
    }

    public function setTransferDate(DateTimeInterface $transferDate): self
    {
        $this->transferDate = $transferDate;

        return $this;
    }

    public function getTransfersEgg(): ?int
    {
        return $this->transfersEgg;
    }

    public function setTransfersEgg(int $transfersEgg): self
    {
        $this->transfersEgg = $transfersEgg;

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
            $inputsFarmDelivery->setTransfers($this);
        }

        return $this;
    }

    public function removeInputsFarmDelivery(InputsFarmDelivery $inputsFarmDelivery): self
    {
        if ($this->inputsFarmDelivery->removeElement($inputsFarmDelivery)) {
            // set the owning side to null (unless already changed)
            if ($inputsFarmDelivery->getTransfers() === $this) {
                $inputsFarmDelivery->setTransfers(null);
            }
        }

        return $this;
    }
}
