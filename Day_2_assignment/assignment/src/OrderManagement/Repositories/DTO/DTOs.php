<?php

namespace OrderManagement\Repositories\DTO;

class ReservationRequest
{
    public int $productId;
    public int $warehouseId;
    public int $quantity;
    public int $expiresInSeconds;

    public function __construct(int $productId, int $warehouseId, int $quantity, int $expiresInSeconds = 300)
    {
        $this->productId = $productId;
        $this->warehouseId = $warehouseId;
        $this->quantity = $quantity;
        $this->expiresInSeconds = $expiresInSeconds;
    }
}

class ReservationResult
{
    public bool $success;
    public ?int $reservationId;
    public string $message;

    public function __construct(bool $success, ?int $reservationId = null, string $message = '')
    {
        $this->success = $success;
        $this->reservationId = $reservationId;
        $this->message = $message;
    }
}

class StockTransfer
{
    public int $productId;
    public int $fromWarehouseId;
    public int $toWarehouseId;
    public int $quantity;

    public function __construct(int $productId, int $fromWarehouseId, int $toWarehouseId, int $quantity)
    {
        $this->productId = $productId;
        $this->fromWarehouseId = $fromWarehouseId;
        $this->toWarehouseId = $toWarehouseId;
        $this->quantity = $quantity;
    }
}

class StockAdjustment
{
    public int $productId;
    public int $warehouseId;
    public int $quantityChange;
    public string $reason;

    public function __construct(int $productId, int $warehouseId, int $quantityChange, string $reason = '')
    {
        $this->productId = $productId;
        $this->warehouseId = $warehouseId;
        $this->quantityChange = $quantityChange;
        $this->reason = $reason;
    }
}
