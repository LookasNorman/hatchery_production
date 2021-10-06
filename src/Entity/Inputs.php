<?php

namespace App\Entity;

use App\Repository\InputsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=InputsRepository::class)
 */
class Inputs
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
     *     min=3,
     *     max=50,
     *     minMessage = "eggs_inputs.name.min",
     *     maxMessage = "eggs_inputs.name.max"
     * )
     * @Assert\NotNull()
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     * @Assert\NotNull()
     */
    private $inputDate;

    /**
     * @ORM\OneToMany(targetEntity=InputsDetails::class, mappedBy="eggInput")
     */
    private $eggsInputsDetails;

    /**
     * @ORM\OneToMany(targetEntity=InputsFarm::class, mappedBy="eggInput")
     */
    private $inputsFarms;

    public function __construct()
    {
        $this->eggsInputsDetails = new ArrayCollection();
        $this->inputsFarms = new ArrayCollection();
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
        $this->inputDate = $inputDate->sub(new \DateInterval('P21DT5H'));

        return $this;
    }

    /**
     * @return Collection|InputsDetails[]
     */
    public function getEggsInputsDetails(): Collection
    {
        return $this->eggsInputsDetails;
    }

    public function addEggsInputsDetail(InputsDetails $eggsInputsDetail): self
    {
        if (!$this->eggsInputsDetails->contains($eggsInputsDetail)) {
            $this->eggsInputsDetails[] = $eggsInputsDetail;
            $eggsInputsDetail->setEggInput($this);
        }

        return $this;
    }

    public function removeEggsInputsDetail(InputsDetails $eggsInputsDetail): self
    {
        if ($this->eggsInputsDetails->removeElement($eggsInputsDetail)) {
            // set the owning side to null (unless already changed)
            if ($eggsInputsDetail->getEggInput() === $this) {
                $eggsInputsDetail->setEggInput(null);
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
            $inputsFarm->setEggInput($this);
        }

        return $this;
    }

    public function removeInputsFarm(InputsFarm $inputsFarm): self
    {
        if ($this->inputsFarms->removeElement($inputsFarm)) {
            // set the owning side to null (unless already changed)
            if ($inputsFarm->getEggInput() === $this) {
                $inputsFarm->setEggInput(null);
            }
        }

        return $this;
    }
}
