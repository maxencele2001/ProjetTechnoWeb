<?php

namespace App\DataFixtures;

use App\Entity\Plat;
use App\Entity\Restaurant;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private const MAX_INDEX = 10;
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
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
                //->setType('Chinois')
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

        $user = new User();
        $user
            ->setEmail('admin@toor.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->encoder->encodePassword(
                $user,'toor'
            ))
            ->setName('admin')
            ->setFirstName('admin')
            ->setAddress('15 rue dla street')
            ->setPostalCode('69000')
            ->setCity('Lyon')
            ->setBalance(500);

        $manager->persist($user);

        $manager->flush();
    }
}
