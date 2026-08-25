<?php

declare(strict_types=1);

final class DashboardController
{
    public function __construct(private readonly DashboardService $service)
    {
    }

    public function stats(): void
    {
        $this->success($this->service->estadisticas());
    }

    public function recentOrders(mixed $limit): void
    {
        try {
            $validatedLimit = DashboardValidator::limite($limit);
        } catch (InvalidArgumentException $exception) {
            $this->error(422, $exception->getMessage());
            return;
        }
        $this->success($this->service->pedidosRecientes($validatedLimit));
    }

    private function success(array $data): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }

    public function error(int $status, string $message): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => ['message' => $message]], JSON_UNESCAPED_UNICODE);
    }
}
