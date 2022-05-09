<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\City;
use App\Entity\Client;
use App\Entity\Contact;
use App\Entity\Country;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for($i = 1; $i <= 5; $i++)
        {
            $country = new Country();
            $country->setName('Country name ' . $i);
            $manager->persist($country);
        }
        $client = new Client();

        // and save user in db
        $manager->flush();
    }
}
