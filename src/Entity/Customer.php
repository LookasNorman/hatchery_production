<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CustomerRepository::class)
 */
class Customer
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
    private $name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $phoneNumber;

    /**
     * @ORM\OneToMany(targetEntity=ChicksRecipient::class, mappedBy="customer")
     */
    private $chicksRecipients;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $postCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $streetNumber;

    /**
     * @ORM\ManyToOne(targetEntity=ChickIntegration::class, inversedBy="customer")
     */
    private $chickIntegration;

    public function __construct()
    {
        $this->chicksRecipients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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
     * @return Collection|ChicksRecipient[]
     */
    public function getChicksRecipients(): Collection
    {
        return $this->chicksRecipients;
    }

    public function addChicksRecipient(ChicksRecipient $chicksRecipient): self
    {
        if (!$this->chicksRecipients->contains($chicksRecipient)) {
            $this->chicksRecipients[] = $chicksRecipient;
            $chicksRecipient->setCustomer($this);
        }

        return $this;
    }

    public function removeChicksRecipient(ChicksRecipient $chicksRecipient): self
    {
        if ($this->chicksRecipients->removeElement($chicksRecipient)) {
            // set the owning side to null (unless already changed)
            if ($chicksRecipient->getCustomer() === $this) {
                $chicksRecipient->setCustomer(null);
            }
        }

        return $this;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(?string $postCode): self
    {
        $this->postCode = $postCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(?string $streetNumber): self
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getChickIntegration(): ?ChickIntegration
    {
        return $this->chickIntegration;
    }

    public function setChickIntegration(?ChickIntegration $chickIntegration): self
    {
        $this->chickIntegration = $chickIntegration;

        return $this;
    }

}
