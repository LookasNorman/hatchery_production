<?php

namespace App\Entity;

use App\Repository\CustomerBuildingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CustomerBuildingRepository::class)
 */
class CustomerBuilding
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
     * @ORM\ManyToOne(targetEntity=ChicksRecipient::class, inversedBy="customerBuildings")
     */
    private $farm;

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

    public function getFarm(): ?ChicksRecipient
    {
        return $this->farm;
    }

    public function setFarm(?ChicksRecipient $farm): self
    {
        $this->farm = $farm;

        return $this;
    }
}
