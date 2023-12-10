<?php

declare(strict_types=1);

namespace App\Tests\helper;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;

trait FunctionalTrait
{
    protected function getProductRepository(): ProductRepository
    {
        $repository = static::getContainer()->get(ProductRepository::class);
        if (!$repository instanceof ProductRepository) {
            throw new LogicException('ProductRepository not found.');
        }

        return $repository;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        if (!$em instanceof EntityManagerInterface) {
            throw new LogicException('EntityManager not found.');
        }

        return $em;
    }
}