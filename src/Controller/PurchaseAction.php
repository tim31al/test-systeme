<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\PaymentSystem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PurchaseAction extends AbstractController
{
    public function __construct(private readonly PaymentSystem $service) {}

    /**
     * @throws \App\Exception\LoggedException
     */
    #[Route(path: '/purchase', name: 'purchase', methods: 'POST')]
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->request->all();
        $dto  = $this->service->process($data, PaymentSystem::METHOD_PAY);

        return $this->json($dto, $dto->getCode(), [], ['groups' => 'api']);
    }
}