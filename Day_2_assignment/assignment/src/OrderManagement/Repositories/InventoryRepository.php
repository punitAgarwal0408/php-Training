<?php

namespace OrderManagement\Repositories;

use PDO;
use OrderManagement\Entities\Inventory;
use OrderManagement\Repositories\DTO\ReservationRequest;
use OrderManagement\Repositories\DTO\ReservationResult;
use OrderManagement\Repositories\DTO\StockTransfer;
use OrderManagement\Repositories\DTO\StockAdjustment;

class InventoryRepository implements InventoryRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function beginTransactionForDriver(): void
    {
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite') {
            // Use BEGIN IMMEDIATE to acquire a write lock in SQLite
            $this->pdo->exec('BEGIN IMMEDIATE');
        } else {
            $this->pdo->beginTransaction();
        }
    }

    private function rollbackForDriver(): void
    {
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite') {
            // If we used exec to begin, use exec to rollback as inTransaction may be false
            try {
                $this->pdo->exec('ROLLBACK');
            } catch (\Throwable $e) {
                // ignore rollback errors (e.g., no active transaction)
            }
        } else {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
        }
    }

    public function findByProductAndWarehouse(int $productId, int $warehouseId): ?Inventory
    {
        $stmt = $this->pdo->prepare('SELECT * FROM inventory WHERE product_id = ? AND warehouse_id = ?');
        $stmt->execute([$productId, $warehouseId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;
        return new Inventory($row);
    }

    public function reserveStock(ReservationRequest $request): ReservationResult
    {
        $this->beginTransactionForDriver();
        try {
            // fetch current row
            $stmt = $this->pdo->prepare('SELECT * FROM inventory WHERE product_id = ? AND warehouse_id = ?');
            $stmt->execute([$request->productId, $request->warehouseId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                $this->rollbackForDriver();
                return new ReservationResult(false, null, 'Inventory row not found');
            }

            $inventory = new Inventory($row);

            if ($inventory->quantity_on_hand < $request->quantity) {
                $this->rollbackForDriver();
                return new ReservationResult(false, null, 'Not enough available stock');
            }

            // optimistic update using version
            $stmt = $this->pdo->prepare('UPDATE inventory SET quantity_on_hand = quantity_on_hand - ?, quantity_reserved = quantity_reserved + ?, version = version + 1 WHERE product_id = ? AND warehouse_id = ? AND version = ? AND quantity_on_hand >= ?');
            $stmt->execute([$request->quantity, $request->quantity, $request->productId, $request->warehouseId, $inventory->version, $request->quantity]);

            if ($stmt->rowCount() !== 1) {
                $this->rollbackForDriver();
                return new ReservationResult(false, null, 'Concurrent update conflict');
            }

            // insert reservation
            $expiresAt = (new \DateTime())->add(new \DateInterval('PT' . $request->expiresInSeconds . 'S'))->format('Y-m-d H:i:s');
            $ins = $this->pdo->prepare('INSERT INTO reservations (product_id, warehouse_id, quantity, expires_at, status) VALUES (?, ?, ?, ?, ?)');
            $ins->execute([$request->productId, $request->warehouseId, $request->quantity, $expiresAt, 'active']);
            $reservationId = (int)$this->pdo->lastInsertId();

            // insert stock movement
            $mv = $this->pdo->prepare('INSERT INTO stock_movements (product_id, warehouse_id, quantity_change, movement_type, reference_id) VALUES (?, ?, ?, ?, ?)');
            $mv->execute([$request->productId, $request->warehouseId, -$request->quantity, 'reservation', $reservationId]);

            $this->pdo->commit();
            return new ReservationResult(true, $reservationId, 'Reserved');
        } catch (\Throwable $e) {
            $this->rollbackForDriver();
            return new ReservationResult(false, null, 'Error: ' . $e->getMessage());
        }
    }

    public function releaseReservation(int $reservationId): void
    {
        $this->beginTransactionForDriver();
        try {
            $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            if ($driver === 'sqlite') {
                $stmt = $this->pdo->prepare('SELECT * FROM reservations WHERE id = ?');
            } else {
                $stmt = $this->pdo->prepare('SELECT * FROM reservations WHERE id = ? FOR UPDATE');
            }
            $stmt->execute([$reservationId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                $this->rollbackForDriver();
                throw new \RuntimeException('Reservation not found');
            }
            if ($row['status'] !== 'active') {
                $this->rollbackForDriver();
                throw new \RuntimeException('Reservation not active');
            }
            $productId = (int)$row['product_id'];
            $warehouseId = (int)$row['warehouse_id'];
            $quantity = (int)$row['quantity'];

            // attempt optimistic update to move reserved -> on hand
            $invStmt = $this->pdo->prepare('SELECT * FROM inventory WHERE product_id = ? AND warehouse_id = ?');
            $invStmt->execute([$productId, $warehouseId]);
            $invRow = $invStmt->fetch(PDO::FETCH_ASSOC);
            if (!$invRow) {
                $this->rollbackForDriver();
                throw new \RuntimeException('Inventory row not found');
            }
            $inv = new Inventory($invRow);

            $upd = $this->pdo->prepare('UPDATE inventory SET quantity_on_hand = quantity_on_hand + ?, quantity_reserved = quantity_reserved - ?, version = version + 1 WHERE product_id = ? AND warehouse_id = ? AND version = ? AND quantity_reserved >= ?');
            $upd->execute([$quantity, $quantity, $productId, $warehouseId, $inv->version, $quantity]);
            if ($upd->rowCount() !== 1) {
                $this->rollbackForDriver();
                throw new \RuntimeException('Concurrent inventory update conflict on release');
            }

            $this->pdo->prepare('UPDATE reservations SET status = ? WHERE id = ?')->execute(['released', $reservationId]);

            $mv = $this->pdo->prepare('INSERT INTO stock_movements (product_id, warehouse_id, quantity_change, movement_type, reference_id) VALUES (?, ?, ?, ?, ?)');
            $mv->execute([$productId, $warehouseId, $quantity, 'release', $reservationId]);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->rollbackForDriver();
            throw $e;
        }
    }

    public function transferStock(StockTransfer $transfer): void
    {
        $this->beginTransactionForDriver();
        try {
            // debit from source
            $stmt = $this->pdo->prepare('SELECT * FROM inventory WHERE product_id = ? AND warehouse_id = ?');
            $stmt->execute([$transfer->productId, $transfer->fromWarehouseId]);
            $fromRow = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$fromRow) throw new \RuntimeException('Source inventory not found');

            $fromInv = new Inventory($fromRow);
            if ($fromInv->quantity_on_hand < $transfer->quantity) throw new \RuntimeException('Insufficient stock to transfer');

            $upd1 = $this->pdo->prepare('UPDATE inventory SET quantity_on_hand = quantity_on_hand - ?, version = version + 1 WHERE product_id = ? AND warehouse_id = ? AND version = ? AND quantity_on_hand >= ?');
            $upd1->execute([$transfer->quantity, $transfer->productId, $transfer->fromWarehouseId, $fromInv->version, $transfer->quantity]);
            if ($upd1->rowCount() !== 1) throw new \RuntimeException('Concurrent conflict debiting source');

            // credit destination (insert if missing)
            $stmt2 = $this->pdo->prepare('SELECT * FROM inventory WHERE product_id = ? AND warehouse_id = ?');
            $stmt2->execute([$transfer->productId, $transfer->toWarehouseId]);
            $toRow = $stmt2->fetch(PDO::FETCH_ASSOC);
            if (!$toRow) {
                $ins = $this->pdo->prepare('INSERT INTO inventory (product_id, warehouse_id, quantity_on_hand, quantity_reserved, version) VALUES (?, ?, ?, 0, 1)');
                $ins->execute([$transfer->productId, $transfer->toWarehouseId, $transfer->quantity]);
            } else {
                $toInv = new Inventory($toRow);
                $upd2 = $this->pdo->prepare('UPDATE inventory SET quantity_on_hand = quantity_on_hand + ?, version = version + 1 WHERE product_id = ? AND warehouse_id = ? AND version = ?');
                $upd2->execute([$transfer->quantity, $transfer->productId, $transfer->toWarehouseId, $toInv->version]);
                if ($upd2->rowCount() !== 1) throw new \RuntimeException('Concurrent conflict crediting dest');
            }

            $mvOut = $this->pdo->prepare('INSERT INTO stock_movements (product_id, warehouse_id, quantity_change, movement_type) VALUES (?, ?, ?, ?)');
            $mvOut->execute([$transfer->productId, $transfer->fromWarehouseId, -$transfer->quantity, 'transfer_out']);
            $mvIn = $this->pdo->prepare('INSERT INTO stock_movements (product_id, warehouse_id, quantity_change, movement_type) VALUES (?, ?, ?, ?)');
            $mvIn->execute([$transfer->productId, $transfer->toWarehouseId, $transfer->quantity, 'transfer_in']);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->rollbackForDriver();
            throw $e;
        }
    }

    public function adjustStock(StockAdjustment $adjustment): void
    {
        $this->beginTransactionForDriver();
        try {
            $stmt = $this->pdo->prepare('SELECT * FROM inventory WHERE product_id = ? AND warehouse_id = ?');
            $stmt->execute([$adjustment->productId, $adjustment->warehouseId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                // insert if not exists
                $ins = $this->pdo->prepare('INSERT INTO inventory (product_id, warehouse_id, quantity_on_hand, quantity_reserved, version) VALUES (?, ?, ?, 0, 1)');
                $ins->execute([$adjustment->productId, $adjustment->warehouseId, max(0, $adjustment->quantityChange)]);
            } else {
                $inv = new Inventory($row);
                $upd = $this->pdo->prepare('UPDATE inventory SET quantity_on_hand = quantity_on_hand + ?, version = version + 1 WHERE product_id = ? AND warehouse_id = ? AND version = ?');
                $upd->execute([$adjustment->quantityChange, $adjustment->productId, $adjustment->warehouseId, $inv->version]);
                if ($upd->rowCount() !== 1) throw new \RuntimeException('Concurrent inventory conflict on adjust');
            }

            $mv = $this->pdo->prepare('INSERT INTO stock_movements (product_id, warehouse_id, quantity_change, movement_type, reference_id) VALUES (?, ?, ?, ?, NULL)');
            $mv->execute([$adjustment->productId, $adjustment->warehouseId, $adjustment->quantityChange, 'adjustment']);

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->rollbackForDriver();
            throw $e;
        }
    }
}
