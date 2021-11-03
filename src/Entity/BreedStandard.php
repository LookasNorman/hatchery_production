<?php

namespace App\Entity;

use App\Repository\BreedStandardRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BreedStandardRepository::class)
 * @Auditable()
 */
class BreedStandard
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Breed::class, inversedBy="breedStandards")
     * @ORM\JoinColumn(nullable=false)
     */
    private $breed;

    /**
     * @ORM\Column(type="integer")
     */
    private $week;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private $hatchingEggsWeek;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getWeek(): ?int
    {
        return $this->week;
    }

    public function setWeek(int $week): self
    {
        $this->week = $week;

        return $this;
    }

    public function getHatchingEggsWeek(): ?string
    {
        return $this->hatchingEggsWeek;
    }

    public function setHatchingEggsWeek(string $hatchingEggsWeek): self
    {
        $this->hatchingEggsWeek = $hatchingEggsWeek;

        return $this;
    }
}
