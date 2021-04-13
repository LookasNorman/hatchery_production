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
     *      min = 4,
     *      max = 20,
     *      minMessage = "breed.name.min",
     *      maxMessage = "breed.name.max"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Length(
     *      min = 3,
     *      max = 20,
     *      minMessage = "breed.type.min",
     *      maxMessage = "breed.type.max"
     * )
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=Herds::class, mappedBy="breed")
     */
    private $herds;

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
}
