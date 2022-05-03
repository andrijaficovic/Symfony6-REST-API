<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Client;
use App\Entity\Contact;
use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for($j=1; $j<=5; $j++)
        {
            $client = new Client();
            $client->setName('Client name ' . $j);
            $client->setCompanyRegistrationNumber(123 . $j);
            $client->setTin('1234'.$j.'456');
            $client->setWebsite('client'.$j.'website.com');
            for($i=1; $i<=3; $i++)
            {
                $contact = new Contact();
                $contact->setName('Contact name ' . $i);
                $contact->setSurname('Contact surname ' . $i);
                $contact->setEmail('contact'.$i.'@gmail.com');
                $contact->setPhoneNumber('+'.$i.'-23456');
                $contact->setClient($client);
                $manager->persist($contact);
            }
            $manager->persist($client);
        }
        $manager->flush();
    }
}
