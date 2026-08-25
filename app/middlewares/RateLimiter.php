<?php

declare(strict_types=1);

final class RateLimiter
{
    public function __construct(private readonly string $directory)
    {
    }

    public function consume(string $scope, string $identifier, int $maxAttempts, int $windowSeconds): bool
    {
        if (!is_dir($this->directory) && !mkdir($this->directory, 0700, true) && !is_dir($this->directory)) {
            throw new RuntimeException('No se pudo inicializar el almacenamiento del limitador.');
        }

        $path = $this->directory . DIRECTORY_SEPARATOR . hash('sha256', $scope . '|' . $identifier) . '.json';
        $handle = fopen($path, 'c+');
        if ($handle === false || !flock($handle, LOCK_EX)) {
            throw new RuntimeException('No se pudo bloquear el almacenamiento del limitador.');
        }

        try {
            $raw = stream_get_contents($handle);
            $attempts = is_string($raw) ? json_decode($raw, true) : [];
            $cutoff = time() - $windowSeconds;
            $attempts = array_values(array_filter(
                is_array($attempts) ? $attempts : [],
                static fn (mixed $time): bool => is_int($time) && $time >= $cutoff
            ));
            if (count($attempts) >= $maxAttempts) {
                return false;
            }
            $attempts[] = time();
            rewind($handle);
            ftruncate($handle, 0);
            fwrite($handle, json_encode($attempts, JSON_THROW_ON_ERROR));
            fflush($handle);

            return true;
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
