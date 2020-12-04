<?php

namespace App\DataFixtures;

use App\Entity\Plat;
use App\Entity\Restaurant;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    private const MAX_INDEX = 10;
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        $restaurants = [];
        $maxIndex = self::MAX_INDEX-1;
        

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
            $restaurants[] = $restaurant;
        }

        for($i=0;$i<25;$i++){
            $plat = new Plat();
            $plat
                ->setName($faker->words(1, true))
                ->setImage($faker->imageUrl())
                ->setPrice($faker->randomFloat(2,0,20))
                ->setRestaurants($restaurants[$faker->numberBetween(0,$maxIndex)]);
            $manager->persist($plat);
        }
        
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
