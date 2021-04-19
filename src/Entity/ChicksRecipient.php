<?php

namespace App\Entity;

use App\Repository\ChicksRecipientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ChicksRecipientRepository::class)
 */
class ChicksRecipient
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
     *     min=5,
     *     max=50,
     *     minMessage = "chicks_recipient.name.min",
     *     maxMessage = "chicks_recipient.name.max"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Email(
     *     message = "chicks_recipient.email",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     * @Assert\Length(
     *     min=9,
     *     max=30,
     *     minMessage = "chicks_recipient.phone_number.min",
     *     maxMessage = "chicks_recipient.phone_number.max"
     * )
     */
    private $phoneNumber;

    /**
     * @ORM\OneToMany(targetEntity=EggsInputsDetails::class, mappedBy="chcicksRecipient")
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

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
            $eggsInputsDetail->setChicksRecipient($this);
        }

        return $this;
    }

    public function removeEggsInputsDetail(EggsInputsDetails $eggsInputsDetail): self
    {
        if ($this->eggsInputsDetails->removeElement($eggsInputsDetail)) {
            // set the owning side to null (unless already changed)
            if ($eggsInputsDetail->getChicksRecipient() === $this) {
                $eggsInputsDetail->setChicksRecipient(null);
            }
        }

        return $this;
    }
}