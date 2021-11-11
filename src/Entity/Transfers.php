<?php

namespace App\Entity;

use App\Repository\TransfersRepository;
use DateTimeInterface;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=TransfersRepository::class)
 * @Auditable()
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
     * @ORM\ManyToOne(targetEntity=InputsFarm::class, inversedBy="transfers")
     */
    private $farm;

    /**
     * @ORM\ManyToOne(targetEntity=Herds::class, inversedBy="transfers")
     */
    private $herd;

    /**
     * @ORM\ManyToOne(targetEntity=Inputs::class, inversedBy="transfers")
     */
    private $input;

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

    public function getFarm(): ?InputsFarm
    {
        return $this->farm;
    }

    public function setFarm(?InputsFarm $farm): self
    {
        $this->farm = $farm;

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

    public function getInput(): ?Inputs
    {
        return $this->input;
    }

    public function setInput(?Inputs $input): self
    {
        $this->input = $input;

        return $this;
    }

}
