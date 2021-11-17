<?php

namespace App\Entity;

use App\Repository\CustomerPhoneRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CustomerPhoneRepository::class)
 * @Auditable()
 */
class CustomerPhone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="customerPhones")
     * @ORM\JoinColumn(nullable=false)
     */
    private $customer;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=70, nullable=true)
     */
    private $person;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $jobPosition;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getPerson(): ?string
    {
        return $this->person;
    }

    public function setPerson(?string $person): self
    {
        $this->person = $person;

        return $this;
    }

    public function getJobPosition(): ?string
    {
        return $this->jobPosition;
    }

    public function setJobPosition(?string $jobPosition): self
    {
        $this->jobPosition = $jobPosition;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
