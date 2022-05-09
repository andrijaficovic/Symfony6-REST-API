<?php

namespace App\Service;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ClientGenerator
{
    private $em;

    /**
     * @param $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function updateClient($client, $user, $name, $companyRegistrationNumber, $tin, $website = null)
    {
        //check if data is set
        //website can be null
        if(empty($name) || empty($companyRegistrationNumber) || empty($tin))
        {
            throw new BadRequestException('Fields can not be blank');
        }

        //inserting record in database
        $client->setName($name);
        $client->setCompanyRegistrationNumber($companyRegistrationNumber);
        $client->setTin($tin);
        $client->setWebsite($website);
        $client->setUser($user);
        $this->em->persist($client);
        $this->em->flush();
    }

    public function createClient($user, $name, $companyRegistrationNumber, $tin, $website = null)
    {
        //check if data is set
        //website can be null
        if(empty($name) || empty($companyRegistrationNumber) || empty($tin))
        {
            throw new BadRequestException('Fields can not be blank');
        }

        //inserting record in database
        $client = new Client();
        $client->setName($name);
        $client->setCompanyRegistrationNumber($companyRegistrationNumber);
        $client->setTin($tin);
        $client->setWebsite($website);
        $client->setUser($user);
        return $client;
    }
}