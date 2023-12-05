<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class DashboardAction
{
    #[Route('/health', name: 'app_health')]
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(['message' => 'Success']);
    }
}
