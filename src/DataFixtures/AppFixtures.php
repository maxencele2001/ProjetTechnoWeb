<?php

namespace App\DataFixtures;

use App\Entity\Restaurant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        

        for($i=0;$i<25;$i++){
            $restaurant = new Restaurant();
            $restaurant
                ->setName($faker->words(1, true))
                ->setDescription($faker->text(20))
                ->setLogo($faker->imageUrl())
                ->setAddress('15 rue dla street')
                ->setPostalCode('69000')
                ->setCity('Lyon')
                ->setEmail('maxence.crosse@ynov.com')
                ->setType('Chinois')
                ->setBalance(500);
            $manager->persist($restaurant);
        }
        
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
