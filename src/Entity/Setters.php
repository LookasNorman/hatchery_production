<?php

namespace App\Entity;

use App\Repository\SettersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SettersRepository::class)
 */
class Setters
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
     *     minMessage="setters.name.min",
     *     maxMessage="setters.name.max",
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=4)
     * @Assert\Length(
     *     allowEmptyString=false,
     *     min=2,
     *     max=4,
     *     minMessage="setters.shortname.min",
     *     maxMessage="setters.shortname.max",
     * )
     */
    private $shortname;

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
}
