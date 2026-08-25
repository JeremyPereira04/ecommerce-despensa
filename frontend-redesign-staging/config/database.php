<?php

declare(strict_types=1);

final class CConexion
{
    public function conexionBD(): PDO
    {
        $driver = $this->env('DB_DRIVER', 'pgsql');
        if ($driver !== 'pgsql') {
            throw new RuntimeException('This application currently requires PostgreSQL (DB_DRIVER=pgsql).');
        }

        $host = $this->env('DB_HOST', '127.0.0.1');
        $port = $this->env('DB_PORT', '5432');
        $database = $this->requiredEnv('DB_NAME');
        $user = $this->requiredEnv('DB_USER');
        $password = $this->requiredEnv('DB_PASSWORD');

        $dsn = sprintf('%s:host=%s;port=%s;dbname=%s', $driver, $host, $port, $database);
        $dsn .= ';sslmode=' . $this->env('DB_SSLMODE', 'prefer');

        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_PERSISTENT => false,
        ]);
    }

    private function requiredEnv(string $name): string
    {
        $value = getenv($name);
        if (!is_string($value) || trim($value) === '') {
            throw new RuntimeException(sprintf('Missing required environment variable: %s', $name));
        }

        return $value;
    }

    private function env(string $name, string $default): string
    {
        $value = getenv($name);

        return is_string($value) && trim($value) !== '' ? $value : $default;
    }
}
