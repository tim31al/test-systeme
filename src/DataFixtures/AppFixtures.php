<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->productGenerator() as $row) {
            [$name, $price] = $row;

            $product = new Product();
            $product
                ->setName($name)
                ->setPrice($price);

            $manager->persist($product);
        }

        $manager->flush();
    }

    private function productGenerator(): \Generator
    {
        yield ['Iphone', 100];
        yield ['Наушники', 20];
        yield ['Чехол', 10];
    }
}
