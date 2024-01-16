<?php

namespace App\DataFixtures;

use App\Entity\FoodTruck;
use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // create 10 foodtruck
        for ($i = 0; $i < 10; $i++) {
            $foodTruck = new FoodTruck();
            $foodTruck->setName('foodtruck_' . $i + 1);
            $manager->persist($foodTruck);
        }
        // create 7 locations
        for ($i = 0; $i < 7; $i++) {
            $location = (new Location())->setName('location_' . $i + 1);
            $manager->persist($location);
        }

        $manager->flush();
    }
}
