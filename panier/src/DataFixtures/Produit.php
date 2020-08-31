<?php

namespace App\DataFixtures;

use Bezhanov\Faker\Provider\Commerce;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Liior\Faker\Prices;

class Produit extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $faker = Factory::create();
        $faker->addProvider(new Commerce($faker));
        $faker->addProvider(new Prices($faker));

        for ($i = 0; $i < 10; $i++) {
            $product = new \App\Entity\Produit();
            $product->setNomProduit($faker->productName)
                ->setPrixProduit($faker->price(20, 200))
                ->setImage($faker->imageUrl(400, 400));

            $manager->persist($product);
        }

        $manager->flush();

    }
}
