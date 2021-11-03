<?php

namespace App\Entity;

use App\Repository\DriverRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DriverRepository::class)
 * @Auditable()
 */
class Driver
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $phoneNumber;

    /**
     * @ORM\ManyToMany(targetEntity=TransportList::class, mappedBy="driver")
     */
    private $transportLists;

    public function __construct()
    {
        $this->transportLists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

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
            $transportList->addDriver($this);
        }

        return $this;
    }

    public function removeTransportList(TransportList $transportList): self
    {
        if ($this->transportLists->removeElement($transportList)) {
            $transportList->removeDriver($this);
        }

        return $this;
    }
}
