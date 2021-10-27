<?php

namespace App\Entity;

use App\Repository\ChicksRecipientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ChicksRecipientRepository::class)
 */
class ChicksRecipient
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
     *     min=5,
     *     max=50,
     *     minMessage = "chicks_recipient.name.min",
     *     maxMessage = "chicks_recipient.name.max"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Email(
     *     message = "chicks_recipient.email",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Assert\Length(
     *     allowEmptyString=false,
     *     min=9,
     *     max=30,
     *     minMessage = "chicks_recipient.phone_number.min",
     *     maxMessage = "chicks_recipient.phone_number.max"
     * )
     */
    private $phoneNumber;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="chicksRecipients")
     */
    private $customer;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $postcode;

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
     * @ORM\OneToMany(targetEntity=CustomerBuilding::class, mappedBy="farm")
     */
    private $customerBuildings;

    /**
     * @ORM\OneToMany(targetEntity=InputsFarm::class, mappedBy="chicksFarm")
     */
    private $inputsFarms;

    /**
     * @ORM\OneToMany(targetEntity=PlanDeliveryChick::class, mappedBy="chickFarm")
     */
    private $planDeliveryChicks;

    /**
     * @ORM\OneToMany(targetEntity=Vaccination::class, mappedBy="farm")
     */
    private $vaccinations;

    public function __construct()
    {
        $this->customerBuildings = new ArrayCollection();
        $this->inputsFarms = new ArrayCollection();
        $this->planInputFarms = new ArrayCollection();
        $this->planDeliveryChicks = new ArrayCollection();
        $this->vaccinations = new ArrayCollection();
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

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): self
    {
        $this->postcode = $postcode;

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

    /**
     * @return Collection|CustomerBuilding[]
     */
    public function getCustomerBuildings(): Collection
    {
        return $this->customerBuildings;
    }

    public function addCustomerBuilding(CustomerBuilding $customerBuilding): self
    {
        if (!$this->customerBuildings->contains($customerBuilding)) {
            $this->customerBuildings[] = $customerBuilding;
            $customerBuilding->setFarm($this);
        }

        return $this;
    }

    public function removeCustomerBuilding(CustomerBuilding $customerBuilding): self
    {
        if ($this->customerBuildings->removeElement($customerBuilding)) {
            // set the owning side to null (unless already changed)
            if ($customerBuilding->getFarm() === $this) {
                $customerBuilding->setFarm(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|InputsFarm[]
     */
    public function getInputsFarms(): Collection
    {
        return $this->inputsFarms;
    }

    public function addInputsFarm(InputsFarm $inputsFarm): self
    {
        if (!$this->inputsFarms->contains($inputsFarm)) {
            $this->inputsFarms[] = $inputsFarm;
            $inputsFarm->setChicksFarm($this);
        }

        return $this;
    }

    public function removeInputsFarm(InputsFarm $inputsFarm): self
    {
        if ($this->inputsFarms->removeElement($inputsFarm)) {
            // set the owning side to null (unless already changed)
            if ($inputsFarm->getChicksFarm() === $this) {
                $inputsFarm->setChicksFarm(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PlanDeliveryChick[]
     */
    public function getPlanDeliveryChicks(): Collection
    {
        return $this->planDeliveryChicks;
    }

    public function addPlanDeliveryChick(PlanDeliveryChick $planDeliveryChick): self
    {
        if (!$this->planDeliveryChicks->contains($planDeliveryChick)) {
            $this->planDeliveryChicks[] = $planDeliveryChick;
            $planDeliveryChick->setChickFarm($this);
        }

        return $this;
    }

    public function removePlanDeliveryChick(PlanDeliveryChick $planDeliveryChick): self
    {
        if ($this->planDeliveryChicks->removeElement($planDeliveryChick)) {
            // set the owning side to null (unless already changed)
            if ($planDeliveryChick->getChickFarm() === $this) {
                $planDeliveryChick->setChickFarm(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Vaccination[]
     */
    public function getVaccinations(): Collection
    {
        return $this->vaccinations;
    }

    public function addVaccination(Vaccination $vaccination): self
    {
        if (!$this->vaccinations->contains($vaccination)) {
            $this->vaccinations[] = $vaccination;
            $vaccination->setFarm($this);
        }

        return $this;
    }

    public function removeVaccination(Vaccination $vaccination): self
    {
        if ($this->vaccinations->removeElement($vaccination)) {
            // set the owning side to null (unless already changed)
            if ($vaccination->getFarm() === $this) {
                $vaccination->setFarm(null);
            }
        }

        return $this;
    }

}
