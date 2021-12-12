<?php

namespace App\Entity;

use App\Repository\InputsRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=InputsRepository::class)
 * @Auditable()
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
     * @ORM\OneToMany(targetEntity=InputsFarm::class, mappedBy="eggInput")
     */
    private $inputsFarms;

    /**
     * @ORM\OneToMany(targetEntity=ChickTemperature::class, mappedBy="input")
     */
    private $chickTemperatures;

    /**
     * @ORM\OneToMany(targetEntity=PlanInput::class, mappedBy="input")
     */
    private $planInputs;

    /**
     * @ORM\OneToMany(targetEntity=InputDelivery::class, mappedBy="input")
     */
    private $inputDeliveries;

    /**
     * @ORM\OneToMany(targetEntity=Transfers::class, mappedBy="input")
     */
    private $transfers;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $selectionDate;

    public function __construct()
    {
        $this->inputsFarms = new ArrayCollection();
        $this->chickTemperatures = new ArrayCollection();
        $this->planInputs = new ArrayCollection();
        $this->inputDeliveries = new ArrayCollection();
        $this->transfers = new ArrayCollection();
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

    /**
     * @return Collection|ChickTemperature[]
     */
    public function getChickTemperatures(): Collection
    {
        return $this->chickTemperatures;
    }

    public function addChickTemperature(ChickTemperature $chickTemperature): self
    {
        if (!$this->chickTemperatures->contains($chickTemperature)) {
            $this->chickTemperatures[] = $chickTemperature;
            $chickTemperature->setInput($this);
        }

        return $this;
    }

    public function removeChickTemperature(ChickTemperature $chickTemperature): self
    {
        if ($this->chickTemperatures->removeElement($chickTemperature)) {
            // set the owning side to null (unless already changed)
            if ($chickTemperature->getInput() === $this) {
                $chickTemperature->setInput(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PlanInput[]
     */
    public function getPlanInputs(): Collection
    {
        return $this->planInputs;
    }

    public function addPlanInput(PlanInput $planInput): self
    {
        if (!$this->planInputs->contains($planInput)) {
            $this->planInputs[] = $planInput;
            $planInput->setInput($this);
        }

        return $this;
    }

    public function removePlanInput(PlanInput $planInput): self
    {
        if ($this->planInputs->removeElement($planInput)) {
            // set the owning side to null (unless already changed)
            if ($planInput->getInput() === $this) {
                $planInput->setInput(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|InputDelivery[]
     */
    public function getInputDeliveries(): Collection
    {
        return $this->inputDeliveries;
    }

    public function addInputDelivery(InputDelivery $inputDelivery): self
    {
        if (!$this->inputDeliveries->contains($inputDelivery)) {
            $this->inputDeliveries[] = $inputDelivery;
            $inputDelivery->setInput($this);
        }

        return $this;
    }

    public function removeInputDelivery(InputDelivery $inputDelivery): self
    {
        if ($this->inputDeliveries->removeElement($inputDelivery)) {
            // set the owning side to null (unless already changed)
            if ($inputDelivery->getInput() === $this) {
                $inputDelivery->setInput(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transfers[]
     */
    public function getTransfers(): Collection
    {
        return $this->transfers;
    }

    public function addTransfer(Transfers $transfer): self
    {
        if (!$this->transfers->contains($transfer)) {
            $this->transfers[] = $transfer;
            $transfer->setInput($this);
        }

        return $this;
    }

    public function removeTransfer(Transfers $transfer): self
    {
        if ($this->transfers->removeElement($transfer)) {
            // set the owning side to null (unless already changed)
            if ($transfer->getInput() === $this) {
                $transfer->setInput(null);
            }
        }

        return $this;
    }

    public function getSelectionDate(): ?\DateTimeInterface
    {
        return $this->selectionDate;
    }

    public function setSelectionDate(?\DateTimeInterface $selectionDate): self
    {
        $this->selectionDate = $selectionDate;

        return $this;
    }
}
