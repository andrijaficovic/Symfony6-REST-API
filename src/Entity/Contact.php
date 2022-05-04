<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
/**
 * @ExclusionPolicy("all")
 */
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    /**
     * @Expose
     */
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    /**
     * @Expose
     */
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    /**
     * @Expose
     */
    private $surname;

    #[ORM\Column(type: 'string', length: 255)]
    /**
     * @Expose
     */
    private $phoneNumber;

    #[ORM\Column(type: 'string', length: 255)]
    /**
     * @Expose
     */
    private $email;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'contacts')]
    #[ORM\JoinColumn(nullable: false)]
    private $client;


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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

}
