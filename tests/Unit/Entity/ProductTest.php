<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testGetName(): void
    {
        $product = new Product();
        $this->assertNull($product->getName());

        $product->setName('test');
        $this->assertSame('test', $product->getName());
    }
}
