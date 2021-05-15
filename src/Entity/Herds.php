<?php

namespace App\Entity;

use App\Repository\HerdsRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=HerdsRepository::class)
 */
class Herds
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     *
     * @Assert\Length(
     *      min = 3,
     *      max = 20,
     *      minMessage = "herds.name.min",
     *      maxMessage = "herds.name.max"
     * )
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Supplier::class, inversedBy="herds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $breeder;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date(
     * )
     */
    private $hatchingDate;

    /**
     * @ORM\ManyToOne(targetEntity=Breed::class, inversedBy="herds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $breed;

    /**
     * @ORM\OneToMany(targetEntity=Delivery::class, mappedBy="herd")
     */
    private $eggsDeliveries;

    public function __construct()
    {
        $this->eggsDeliveries = new ArrayCollection();
        $this->deliveries = new ArrayCollection();
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

    public function getBreeder(): ?Supplier
    {
        return $this->breeder;
    }

    public function setBreeder(?Supplier $breeder): self
    {
        $this->breeder = $breeder;

        return $this;
    }

    public function getHatchingDate(): ?DateTimeInterface
    {
        return $this->hatchingDate;
    }

    public function setHatchingDate(DateTimeInterface $hatchingDate): self
    {
        $this->hatchingDate = $hatchingDate;

        return $this;
    }

    public function getBreed(): ?Breed
    {
        return $this->breed;
    }

    public function setBreed(?Breed $breed): self
    {
        $this->breed = $breed;

        return $this;
    }

    /**
     * @return Collection|Delivery[]
     */
    public function getEggsDeliveries(): Collection
    {
        return $this->eggsDeliveries;
    }

    public function addEggsDelivery(Delivery $eggsDelivery): self
    {
        if (!$this->eggsDeliveries->contains($eggsDelivery)) {
            $this->eggsDeliveries[] = $eggsDelivery;
            $eggsDelivery->setHerd($this);
        }

        return $this;
    }

    public function removeEggsDelivery(Delivery $eggsDelivery): self
    {
        if ($this->eggsDeliveries->removeElement($eggsDelivery)) {
            // set the owning side to null (unless already changed)
            if ($eggsDelivery->getHerd() === $this) {
                $eggsDelivery->setHerd(null);
            }
        }

        return $this;
    }
}
