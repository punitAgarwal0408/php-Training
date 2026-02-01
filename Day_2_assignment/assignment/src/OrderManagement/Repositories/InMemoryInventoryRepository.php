<?php

namespace OrderManagement\Repositories;

use OrderManagement\Entities\Inventory;
use OrderManagement\Repositories\DTO\ReservationRequest;
use OrderManagement\Repositories\DTO\ReservationResult;
use OrderManagement\Repositories\DTO\StockTransfer;
use OrderManagement\Repositories\DTO\StockAdjustment;

class InMemoryInventoryRepository implements InventoryRepositoryInterface
{
    private array $inventory = []; // key: "{product}_{warehouse}" => ['product_id'=>..,'warehouse_id'=>..,'quantity_on_hand'=>..,'quantity_reserved'=>..,'version'=>..]
    private array $reservations = []; // id => reservation
    private array $stock_movements = [];
    private int $reservationSeq = 1;

    public function __construct()
    {
    }

    private function key(int $productId, int $warehouseId): string
    {
        return $productId . '_' . $warehouseId;
    }

    public function seedInventory(int $productId, int $warehouseId, int $onHand, int $reserved = 0): void
    {
        $this->inventory[$this->key($productId, $warehouseId)] = [
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'quantity_on_hand' => $onHand,
            'quantity_reserved' => $reserved,
            'version' => 1,
        ];
    }

    public function findByProductAndWarehouse(int $productId, int $warehouseId): ?Inventory
    {
        $k = $this->key($productId, $warehouseId);
        if (!isset($this->inventory[$k])) return null;
        return new Inventory($this->inventory[$k]);
    }

    public function reserveStock(ReservationRequest $request): ReservationResult
    {
        $k = $this->key($request->productId, $request->warehouseId);
        if (!isset($this->inventory[$k])) {
            return new ReservationResult(false, null, 'Inventory row not found');
        }
        $row = &$this->inventory[$k];
        if ($row['quantity_on_hand'] < $request->quantity) {
            return new ReservationResult(false, null, 'Not enough available stock');
        }
        $row['quantity_on_hand'] -= $request->quantity;
        $row['quantity_reserved'] += $request->quantity;
        $row['version']++;

        $expiresAt = (new \DateTime())->add(new \DateInterval('PT' . $request->expiresInSeconds . 'S'));
        $rid = $this->reservationSeq++;
        $this->reservations[$rid] = [
            'id' => $rid,
            'product_id' => $request->productId,
            'warehouse_id' => $request->warehouseId,
            'quantity' => $request->quantity,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            'status' => 'active',
        ];

        $this->stock_movements[] = [
            'product_id' => $request->productId,
            'warehouse_id' => $request->warehouseId,
            'quantity_change' => -$request->quantity,
            'movement_type' => 'reservation',
            'reference_id' => $rid,
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];

        return new ReservationResult(true, $rid, 'Reserved (in-memory)');
    }

    public function releaseReservation(int $reservationId): void
    {
        if (!isset($this->reservations[$reservationId])) {
            throw new \RuntimeException('Reservation not found');
        }
        $res = &$this->reservations[$reservationId];
        if ($res['status'] !== 'active') {
            throw new \RuntimeException('Reservation not active');
        }
        $productId = $res['product_id'];
        $warehouseId = $res['warehouse_id'];
        $quantity = $res['quantity'];

        $k = $this->key($productId, $warehouseId);
        if (!isset($this->inventory[$k])) {
            throw new \RuntimeException('Inventory row not found');
        }
        $row = &$this->inventory[$k];
        $row['quantity_on_hand'] += $quantity;
        $row['quantity_reserved'] -= $quantity;
        $row['version']++;

        $res['status'] = 'released';

        $this->stock_movements[] = [
            'product_id' => $productId,
            'warehouse_id' => $warehouseId,
            'quantity_change' => $quantity,
            'movement_type' => 'release',
            'reference_id' => $reservationId,
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];
    }

    public function transferStock(StockTransfer $transfer): void
    {
        $from = $this->key($transfer->productId, $transfer->fromWarehouseId);
        $to = $this->key($transfer->productId, $transfer->toWarehouseId);
        if (!isset($this->inventory[$from])) throw new \RuntimeException('Source inventory not found');
        if ($this->inventory[$from]['quantity_on_hand'] < $transfer->quantity) throw new \RuntimeException('Insufficient stock to transfer');

        $this->inventory[$from]['quantity_on_hand'] -= $transfer->quantity;
        $this->inventory[$from]['version']++;

        if (!isset($this->inventory[$to])) {
            $this->inventory[$to] = [
                'product_id' => $transfer->productId,
                'warehouse_id' => $transfer->toWarehouseId,
                'quantity_on_hand' => $transfer->quantity,
                'quantity_reserved' => 0,
                'version' => 1,
            ];
        } else {
            $this->inventory[$to]['quantity_on_hand'] += $transfer->quantity;
            $this->inventory[$to]['version']++;
        }

        $this->stock_movements[] = ['product_id' => $transfer->productId, 'warehouse_id' => $transfer->fromWarehouseId, 'quantity_change' => -$transfer->quantity, 'movement_type' => 'transfer_out', 'reference_id' => null, 'created_at' => (new \DateTime())->format('Y-m-d H:i:s')];
        $this->stock_movements[] = ['product_id' => $transfer->productId, 'warehouse_id' => $transfer->toWarehouseId, 'quantity_change' => $transfer->quantity, 'movement_type' => 'transfer_in', 'reference_id' => null, 'created_at' => (new \DateTime())->format('Y-m-d H:i:s')];
    }

    public function adjustStock(StockAdjustment $adjustment): void
    {
        $k = $this->key($adjustment->productId, $adjustment->warehouseId);
        if (!isset($this->inventory[$k])) {
            $this->inventory[$k] = ['product_id' => $adjustment->productId, 'warehouse_id' => $adjustment->warehouseId, 'quantity_on_hand' => max(0, $adjustment->quantityChange), 'quantity_reserved' => 0, 'version' => 1];
        } else {
            $this->inventory[$k]['quantity_on_hand'] += $adjustment->quantityChange;
            $this->inventory[$k]['version']++;
        }

        $this->stock_movements[] = ['product_id' => $adjustment->productId, 'warehouse_id' => $adjustment->warehouseId, 'quantity_change' => $adjustment->quantityChange, 'movement_type' => 'adjustment', 'reference_id' => null, 'created_at' => (new \DateTime())->format('Y-m-d H:i:s')];
    }
}
