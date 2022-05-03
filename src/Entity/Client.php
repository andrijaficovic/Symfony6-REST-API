<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'integer')]
    private $companyRegistrationNumber;

    #[ORM\Column(type: 'integer')]
    private $tin;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $website;

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

    public function getCompanyRegistrationNumber(): ?int
    {
        return $this->companyRegistrationNumber;
    }

    public function setCompanyRegistrationNumber(int $companyRegistrationNumber): self
    {
        $this->companyRegistrationNumber = $companyRegistrationNumber;

        return $this;
    }

    public function getTin(): ?int
    {
        return $this->tin;
    }

    public function setTin(int $tin): self
    {
        $this->tin = $tin;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }
}
