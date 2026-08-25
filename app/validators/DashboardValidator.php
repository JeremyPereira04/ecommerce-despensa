<?php

declare(strict_types=1);

final class DashboardValidator
{
    public static function limite(mixed $valor): int
    {
        if ($valor === null || $valor === '') {
            return 10;
        }
        if (is_array($valor) || filter_var($valor, FILTER_VALIDATE_INT) === false) {
            throw new InvalidArgumentException('El parámetro limit debe ser un número entero entre 1 y 50.');
        }

        $limite = (int) $valor;
        if ($limite < 1 || $limite > 50) {
            throw new InvalidArgumentException('El parámetro limit debe estar entre 1 y 50.');
        }

        return $limite;
    }
}
