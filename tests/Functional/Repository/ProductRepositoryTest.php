<?php

namespace App\Tests\Functional\Repository;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Tests\helper\FunctionalTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductRepositoryTest extends KernelTestCase
{
    use FunctionalTrait;

    private ProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->repository = $this->getProductRepository();
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
