<?php

declare(strict_types=1);

final class AdminApiMiddleware
{
    public function __construct(private readonly PDO $connection, private readonly RateLimiter $rateLimiter)
    {
    }

    public function handle(): bool
    {
        $sessionUser = $_SESSION['admin_user'] ?? $_SESSION['user'] ?? null;
        $id = is_array($sessionUser) ? filter_var($sessionUser['id'] ?? null, FILTER_VALIDATE_INT) : false;
        if ($id === false) {
            return $this->reject(401, 'No autenticado.');
        }

        $statement = $this->connection->prepare(
            'SELECT rol, activo FROM usuarios WHERE id_usuario = :id LIMIT 1'
        );
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $user = $statement->fetch(PDO::FETCH_ASSOC);
        if (!is_array($user)) {
            return $this->reject(401, 'La sesión ya no es válida.');
        }
        $active = in_array($user['activo'] ?? null, [true, 1, '1', 't', 'true'], true);
        if (!$active || ($user['rol'] ?? null) !== 'ADMIN') {
            return $this->reject(403, 'Acceso reservado a administradores activos.');
        }
        if (!$this->rateLimiter->consume('admin-api', (string) $id, 60, 60)) {
            header('Retry-After: 60');
            return $this->reject(429, 'Demasiadas solicitudes. Intentá nuevamente en un minuto.');
        }

        $_SESSION['admin_last_activity'] = time();
        return true;
    }

    private function reject(int $status, string $message): false
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => ['message' => $message]], JSON_UNESCAPED_UNICODE);
        return false;
    }
}
