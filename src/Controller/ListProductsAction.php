<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Список продуктов.
 */
class ListProductsAction extends AbstractController
{
    public function __construct(private readonly ProductRepository $productRepository)
    {
    }

    #[Route('/products', name: 'list_products', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        $data = $this->productRepository->findBy([], ['name' => 'ASC']);
        return $this->json($data);
    }
}