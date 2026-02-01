<?php

namespace OrderManagement\Services;

use OrderManagement\Repositories\InventoryRepositoryInterface;
use OrderManagement\Repositories\DTO\ReservationRequest;
use OrderManagement\Repositories\DTO\StockTransfer;
use OrderManagement\Repositories\DTO\StockAdjustment;

class InventoryAllocationService
{
    private InventoryRepositoryInterface $repo;
    private array $warehouseOrder;

    public function __construct(InventoryRepositoryInterface $repo, array $warehouseOrder = [])
    {
        $this->repo = $repo;
        $this->warehouseOrder = $warehouseOrder; // preferred warehouse ids in order
    }

    /**
     * Try to allocate quantity across warehouses. Returns array of reservation ids.
     */
    public function allocate(int $productId, int $quantity): array
    {
        $remaining = $quantity;
        $reservations = [];

        // If no preference, we will try all warehouses in DB order (not optimal but simple)
        $warehouses = $this->warehouseOrder;

        foreach ($warehouses as $wid) {
            if ($remaining <= 0) break;
            $req = new ReservationRequest($productId, $wid, min($remaining, 999999), 300);
            $res = $this->repo->reserveStock($req);
            if ($res->success && $res->reservationId) {
                $reservations[] = $res->reservationId;
                $remaining -= $req->quantity;
            }
        }

        // If still remaining, try to find other warehouses dynamically (not implemented: requires repository to list warehouses with stock)
        // For demo simplicity, we stop here and leave remaining as backordered

        return ['reservation_ids' => $reservations, 'remaining' => $remaining];
    }

    public function transfer(int $productId, int $fromWarehouseId, int $toWarehouseId, int $quantity): void
    {
        $transfer = new StockTransfer($productId, $fromWarehouseId, $toWarehouseId, $quantity);
        $this->repo->transferStock($transfer);
    }

    public function adjust(int $productId, int $warehouseId, int $quantityChange, string $reason = ''): void
    {
        $adjust = new StockAdjustment($productId, $warehouseId, $quantityChange, $reason);
        $this->repo->adjustStock($adjust);
    }
}
