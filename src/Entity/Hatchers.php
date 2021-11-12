<?php

namespace App\Entity;

use App\Repository\HatchersRepository;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=HatchersRepository::class)
 * @Auditable()
 */
class Hatchers
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
     *     min=4,
     *     max=20,
     *     minMessage="hatchers.name.min",
     *     maxMessage="hatchers.name.max",
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=4)
     * @Assert\Length(
     *     allowEmptyString=false,
     *     min=2,
     *     max=4,
     *     minMessage="hatchers.shortname.min",
     *     maxMessage="hatchers.shortname.max",
     * )
     */
    private $shortname;

    /**
     * @ORM\OneToMany(targetEntity=ChickTemperature::class, mappedBy="hatcher")
     */
    private $chickTemperatures;

    /**
     * @ORM\ManyToMany(targetEntity=Transfers::class, mappedBy="hatchers")
     */
    private $transfers;

    public function __construct()
    {
        $this->chickTemperatures = new ArrayCollection();
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

    public function getShortname(): ?string
    {
        return $this->shortname;
    }

    public function setShortname(string $shortname): self
    {
        $this->shortname = $shortname;

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
            $chickTemperature->setHatcher($this);
        }

        return $this;
    }

    public function removeChickTemperature(ChickTemperature $chickTemperature): self
    {
        if ($this->chickTemperatures->removeElement($chickTemperature)) {
            // set the owning side to null (unless already changed)
            if ($chickTemperature->getHatcher() === $this) {
                $chickTemperature->setHatcher(null);
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
            $transfer->addHatcher($this);
        }

        return $this;
    }

    public function removeTransfer(Transfers $transfer): self
    {
        if ($this->transfers->removeElement($transfer)) {
            $transfer->removeHatcher($this);
        }

        return $this;
    }
}
