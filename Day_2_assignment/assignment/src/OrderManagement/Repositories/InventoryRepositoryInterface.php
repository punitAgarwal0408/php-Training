<?php

namespace OrderManagement\Repositories;

use OrderManagement\Entities\Inventory;
use OrderManagement\Repositories\DTO\ReservationRequest;
use OrderManagement\Repositories\DTO\ReservationResult;
use OrderManagement\Repositories\DTO\StockTransfer;
use OrderManagement\Repositories\DTO\StockAdjustment;

interface InventoryRepositoryInterface
{
    public function findByProductAndWarehouse(int $productId, int $warehouseId): ?Inventory;

    public function reserveStock(ReservationRequest $request): ReservationResult;

    public function releaseReservation(int $reservationId): void;

    public function transferStock(StockTransfer $transfer): void;

    public function adjustStock(StockAdjustment $adjustment): void;
}
