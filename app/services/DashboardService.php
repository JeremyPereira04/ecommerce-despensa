<?php

declare(strict_types=1);

class DashboardService
{
    public function __construct(
        private readonly DashboardRepository $repository,
        private readonly DateTimeZone $timezone,
        private readonly int $limiteStockBajo = 5
    ) {
    }

    public function estadisticas(?DateTimeImmutable $ahora = null): array
    {
        $ahora = ($ahora ?? new DateTimeImmutable('now', $this->timezone))->setTimezone($this->timezone);
        $desde = $ahora->setTime(0, 0);
        $hasta = $desde->modify('+1 day');

        $weekStart=$desde->modify('-6 days');$salesRows=$this->repository->ventasDiarias($weekStart,$hasta,$this->timezone->getName());$salesByDate=[];foreach($salesRows as $row){$salesByDate[$row['fecha']]=$this->numero((string)$row['total']);}$weekly=[];for($day=$weekStart;$day<$hasta;$day=$day->modify('+1 day')){$key=$day->format('Y-m-d');$weekly[]=['fecha'=>$key,'total'=>$salesByDate[$key]??0];}
        return [
            'ventas_hoy' => $this->numero($this->repository->ventasAprobadasEntre($desde, $hasta)),
            'pedidos_pendientes' => $this->repository->contarPedidosPendientes(),
            'productos_stock_bajo' => $this->repository->contarProductosConStockBajo($this->limiteStockBajo),
            'productos_activos'=>$this->repository->contarProductosActivos(),
            'ventas_7_dias'=>$weekly,
            'estados_pedidos'=>array_map(static fn(array $r):array=>['estado'=>(string)$r['estado'],'cantidad'=>(int)$r['cantidad']],$this->repository->estadosPedidos()),
            'stock_bajo'=>$this->repository->productosStockBajo($this->limiteStockBajo),
            'pedidos_recientes'=>$this->pedidosRecientes(5),
        ];
    }

    public function pedidosRecientes(int $limite): array
    {
        return array_map(function (array $pedido): array {
            return [
                'id_pedido' => (int) $pedido['id_pedido'],
                'cliente' => (string) $pedido['cliente'],
                'fecha_pedido' => (string) $pedido['fecha_pedido'],
                'total' => $this->numero((string) $pedido['total']),
                'estado' => (string) $pedido['estado'],
            ];
        }, $this->repository->pedidosRecientes($limite));
    }

    private function numero(string $valor): int|float
    {
        $numero = (float) $valor;

        return floor($numero) === $numero ? (int) $numero : $numero;
    }
}
