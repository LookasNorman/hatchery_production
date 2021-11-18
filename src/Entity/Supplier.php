<?php

namespace App\Entity;

use App\Repository\SupplierRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=SupplierRepository::class)
 * @Auditable()
 */
class Supplier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(
     *     allowEmptyString=false,
     *     min = 5,
     *     max = 50,
     *     minMessage = "egg_supplier.name.min",
     *     maxMessage = "egg_supplier.name.max"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Email(
     *     message = "egg_supplier.email",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Assert\Length(
     *     allowEmptyString=false,
     *     min = 9,
     *     max = 30,
     *     minMessage = "egg_supplier.phone_number.min",
     *     maxMessage = "egg_supplier.phone_number.max"
     * )
     */
    private $phoneNumber;

    /**
     * @ORM\OneToMany(targetEntity=Herds::class, mappedBy="breeder")
     */
    private $herds;

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
    private $streetNubmber;

    public function __construct()
    {
        $this->herds = new ArrayCollection();
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
     * @return Collection|Herds[]
     */
    public function getHerds(): Collection
    {
        return $this->herds;
    }

    public function addHerd(Herds $herd): self
    {
        if (!$this->herds->contains($herd)) {
            $this->herds[] = $herd;
            $herd->setBreeder($this);
        }

        return $this;
    }

    public function removeHerd(Herds $herd): self
    {
        if ($this->herds->removeElement($herd)) {
            // set the owning side to null (unless already changed)
            if ($herd->getBreeder() === $this) {
                $herd->setBreeder(null);
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

    public function getStreetNubmber(): ?string
    {
        return $this->streetNubmber;
    }

    public function setStreetNubmber(?string $streetNubmber): self
    {
        $this->streetNubmber = $streetNubmber;

        return $this;
    }

}
