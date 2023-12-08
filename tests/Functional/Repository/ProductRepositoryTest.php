<?php

namespace App\Tests\Functional\Repository;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductRepositoryTest extends KernelTestCase
{
    private ProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $repository = static::getContainer()->get(ProductRepository::class);
        if (!$repository instanceof ProductRepository) {
            throw new \LogicException('ProductRepository not found.');
        }

        $this->repository = $repository;
    }


    public function testFindAll(): void
    {
        $products = $this->repository->findBy([], ['price' => 'ASC']);

        $this->assertCount(3, $products);
    }

    public function testFindOne(): void
    {
        $product = $this->repository->findOneBy(['name' => 'Iphone']);
        $this->assertInstanceOf(Product::class, $product);
    }
}
