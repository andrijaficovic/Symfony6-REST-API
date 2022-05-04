<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
/**
 * @ExclusionPolicy("all")
 */
class Address
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
    private $street;

    #[ORM\Column(type: 'string', length: 255)]
    /**
     * @Expose
     */
    private $streetNumber;

    #[ORM\Column(type: 'integer')]
    /**
     * @Expose
     */
    private $postalCode;


    #[ORM\ManyToOne(targetEntity: City::class, inversedBy: 'addresses')]
    /**
     * @Expose
     */
    private $city;

    #[ORM\ManyToOne(targetEntity: Country::class)]
    /**
     * @Expose
     */
    private $country;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getStreetNumber(): ?string
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(string $streetNumber): self
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getPostalCode(): ?int
    {
        return $this->postalCode;
    }

    public function setPostalCode(int $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }


    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

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
