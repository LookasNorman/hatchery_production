<?php

namespace App\Entity;

use App\Repository\BreedRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BreedRepository::class)
 */
class Breed
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Length(
     *     allowEmptyString=false,
     *     min = 4,
     *     max = 20,
     *     minMessage = "breed.name.min",
     *     maxMessage = "breed.name.max"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Length(
     *     allowEmptyString=false,
     *     min = 3,
     *     max = 20,
     *     minMessage = "breed.type.min",
     *     maxMessage = "breed.type.max"
     * )
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=Herds::class, mappedBy="breed")
     */
    private $herds;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lighting;

    /**
     * @ORM\OneToMany(targetEntity=BreedStandard::class, mappedBy="breed")
     */
    private $breedStandards;

    /**
     * @ORM\ManyToMany(targetEntity=PlanDeliveryChick::class, mappedBy="breed")
     */
    private $planDeliveryChicks;

    public function __construct()
    {
        $this->herds = new ArrayCollection();
        $this->breedStandards = new ArrayCollection();
        $this->planDeliveryChicks = new ArrayCollection();
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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
            $herd->setBreed($this);
        }

        return $this;
    }

    public function removeHerd(Herds $herd): self
    {
        if ($this->herds->removeElement($herd)) {
            // set the owning side to null (unless already changed)
            if ($herd->getBreed() === $this) {
                $herd->setBreed(null);
            }
        }

        return $this;
    }

    public function getLighting(): ?int
    {
        return $this->lighting;
    }

    public function setLighting(?int $lighting): self
    {
        $this->lighting = $lighting;

        return $this;
    }

    /**
     * @return Collection|BreedStandard[]
     */
    public function getBreedStandards(): Collection
    {
        return $this->breedStandards;
    }

    public function addBreedStandard(BreedStandard $breedStandard): self
    {
        if (!$this->breedStandards->contains($breedStandard)) {
            $this->breedStandards[] = $breedStandard;
            $breedStandard->setBreed($this);
        }

        return $this;
    }

    public function removeBreedStandard(BreedStandard $breedStandard): self
    {
        if ($this->breedStandards->removeElement($breedStandard)) {
            // set the owning side to null (unless already changed)
            if ($breedStandard->getBreed() === $this) {
                $breedStandard->setBreed(null);
            }
        }

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPlanDeliveryChicks(): ArrayCollection
    {
        return $this->planDeliveryChicks;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $planDeliveryChicks
     */
    public function setPlanDeliveryChicks(ArrayCollection $planDeliveryChicks): void
    {
        $this->planDeliveryChicks = $planDeliveryChicks;
    }


}
