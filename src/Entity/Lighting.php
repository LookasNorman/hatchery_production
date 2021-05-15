<?php

namespace App\Entity;

use App\Repository\LightingRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LightingRepository::class)
 */
class Lighting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=InputsDetails::class, inversedBy="eggsInputsLightings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $eggsInputsDetail;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull()
     */
    private $wasteEggs;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date()
     */
    private $lightingDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEggsInputsDetail(): ?InputsDetails
    {
        return $this->eggsInputsDetail;
    }

    public function setEggsInputsDetail(?InputsDetails $eggsInputsDetail): self
    {
        $this->eggsInputsDetail = $eggsInputsDetail;

        return $this;
    }

    public function getWasteEggs(): ?int
    {
        return $this->wasteEggs;
    }

    public function setWasteEggs(int $wasteEggs): self
    {
        $this->wasteEggs = $wasteEggs;

        return $this;
    }

    public function getLightingDate(): ?DateTimeInterface
    {
        return $this->lightingDate;
    }

    public function setLightingDate(DateTimeInterface $lightingDate): self
    {
        $this->lightingDate = $lightingDate;

        return $this;
    }
}
