<?php

namespace App\Entity;

use App\Repository\EggsInputsTransfersRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EggsInputsTransfersRepository::class)
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
     * @ORM\ManyToOne(targetEntity=InputsDetails::class, inversedBy="eggsInputsTransfers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eggsInputsDetail;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull()
     */
    private $wasteEggs;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date()
     */
    private $transferDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEggsInputsDetail(): ?InputsDetails
    {
        return $this->eggsInputsDetail;
    }

    public function setEggsInputsDetail(?InputsDetails $eggsInputsDetail): self
    {
        $this->eggsInputsDetail = $eggsInputsDetail;

        return $this;
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

    public function getTransferDate(): ?DateTimeInterface
    {
        return $this->transferDate;
    }

    public function setTransferDate(DateTimeInterface $transferDate): self
    {
        $this->transferDate = $transferDate;

        return $this;
    }
}
