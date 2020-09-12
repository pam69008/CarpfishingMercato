<?php

namespace App\DataFixtures;

use App\Entity\Articles;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 20; $i++) {
            $article = new Articles();
            $article->setName('product' . $i);
            $article->setPrice(mt_rand(3,10));
            $article->setQuantity(mt_rand(1,100));
            $article->setDescription($faker->text);
            $article->setOutOfPrint(false);

            $manager->persist($article);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
