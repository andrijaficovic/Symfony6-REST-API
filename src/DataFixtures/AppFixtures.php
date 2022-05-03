<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for($i=1; $i<=5; $i++)
        {
            $country = new Country();
            $country->setName('Country name ' . $i);
            for($j=1; $j<=3; $j++)
            {
                $city = new City();
                $city->setName('City name ' . $j);
                $city->setCountry($country);
                $manager->persist($city);
            }
            $manager->persist($country);
        }
        $manager->flush();
    }
}
