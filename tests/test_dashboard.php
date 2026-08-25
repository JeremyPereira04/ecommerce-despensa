<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/repositories/DashboardRepository.php';
require_once dirname(__DIR__) . '/app/services/DashboardService.php';
require_once dirname(__DIR__) . '/app/validators/DashboardValidator.php';

function assert_dashboard(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

final class DashboardRepositoryFake extends DashboardRepository
{
    public DateTimeImmutable $desde;
    public DateTimeImmutable $hasta;
    public int $stockLimit = 0;

    public function __construct()
    {
        parent::__construct(new PDO('sqlite::memory:'));
    }

    public function ventasAprobadasEntre(DateTimeImmutable $desde, DateTimeImmutable $hasta): string
    {
        $this->desde = $desde;
        $this->hasta = $hasta;
        return '0';
    }

    public function contarPedidosPendientes(): int
    {
        return 8;
    }

    public function contarProductosConStockBajo(int $limite): int
    {
        $this->stockLimit = $limite;
        return 5;
    }

    public function pedidosRecientes(int $limite): array
    {
        return [[
            'id_pedido' => '42',
            'cliente' => 'Ana Pérez',
            'fecha_pedido' => '2026-08-25 10:00:00-03',
            'total' => '1250000.00',
            'estado' => 'PENDIENTE',
        ]];
    }
    public function contarProductosActivos(): int{return 86;}
    public function ventasDiarias(DateTimeImmutable $desde,DateTimeImmutable $hasta,string $timezone):array{return [];}
    public function estadosPedidos():array{return [];}
    public function productosStockBajo(int $limite,int $cantidad=5):array{return [];}
}

assert_dashboard(DashboardValidator::limite(null) === 10, 'Default limit must be 10.');
assert_dashboard(DashboardValidator::limite('50') === 50, 'Numeric limit must be normalized.');
foreach (['abc', '-1', '0', '51', '2.5'] as $invalid) {
    try {
        DashboardValidator::limite($invalid);
        throw new RuntimeException('Invalid limit was accepted: ' . $invalid);
    } catch (InvalidArgumentException) {
    }
}

$repository = new DashboardRepositoryFake();
$timezone = new DateTimeZone('America/Asuncion');
$service = new DashboardService($repository, $timezone, 5);
$stats = $service->estadisticas(new DateTimeImmutable('2026-08-25 23:59:59', $timezone));
assert_dashboard($stats['ventas_hoy']===0 && $stats['pedidos_pendientes']===8 && $stats['productos_stock_bajo']===5 && $stats['productos_activos']===86, 'Stats must normalize empty sales and preserve counts.');
assert_dashboard(count($stats['ventas_7_dias'])===7, 'Weekly chart must always contain seven days.');
assert_dashboard($repository->desde->format('Y-m-d H:i:s') === '2026-08-25 00:00:00', 'Start must use Paraguay local midnight.');
assert_dashboard($repository->hasta->format('Y-m-d H:i:s') === '2026-08-26 00:00:00', 'End must be exclusive next midnight.');
assert_dashboard($repository->desde->getTimezone()->getName() === 'America/Asuncion', 'Bounds must retain the configured IANA timezone.');
assert_dashboard($repository->stockLimit === 5, 'Stock equal to configured threshold must be included by repository query.');

$orders = $service->pedidosRecientes(10);
assert_dashboard($orders[0]['id_pedido'] === 42, 'Order ID must be numeric.');
assert_dashboard($orders[0]['total'] === 1250000, 'Order total must be numeric.');
assert_dashboard(!array_key_exists('telefono', $orders[0]), 'Response must not expose unnecessary user data.');

echo "Dashboard tests passed.\n";
