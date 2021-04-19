<?php

namespace App\Entity;

use App\Repository\EggsInputsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EggsInputsRepository::class)
 */
class EggsInputs
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
     *     min=3,
     *     max=50,
     *     minMessage = "eggs_inputs.name.min",
     *     maxMessage = "eggs_inputs.name.max"
     * )
     * @Assert\NotNull()
     */
    private $name;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date()
     * @Assert\NotNull()
     */
    private $inputDate;

    /**
     * @ORM\OneToMany(targetEntity=EggsInputsDetails::class, mappedBy="eggInput")
     */
    private $eggsInputsDetails;

    public function __construct()
    {
        $this->eggsInputsDetails = new ArrayCollection();
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

    public function getInputDate(): ?\DateTimeInterface
    {
        return $this->inputDate;
    }

    public function setInputDate(\DateTimeInterface $inputDate): self
    {
        $this->inputDate = $inputDate;

        return $this;
    }

    /**
     * @return Collection|EggsInputsDetails[]
     */
    public function getEggsInputsDetails(): Collection
    {
        return $this->eggsInputsDetails;
    }

    public function addEggsInputsDetail(EggsInputsDetails $eggsInputsDetail): self
    {
        if (!$this->eggsInputsDetails->contains($eggsInputsDetail)) {
            $this->eggsInputsDetails[] = $eggsInputsDetail;
            $eggsInputsDetail->setEggInput($this);
        }

        return $this;
    }

    public function removeEggsInputsDetail(EggsInputsDetails $eggsInputsDetail): self
    {
        if ($this->eggsInputsDetails->removeElement($eggsInputsDetail)) {
            // set the owning side to null (unless already changed)
            if ($eggsInputsDetail->getEggInput() === $this) {
                $eggsInputsDetail->setEggInput(null);
            }
        }

        return $this;
    }
}