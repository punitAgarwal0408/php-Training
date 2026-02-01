<?php

require __DIR__ . '/../src/OrderManagement/Repositories/InventoryRepositoryInterface.php';
require __DIR__ . '/../src/OrderManagement/Repositories/InventoryRepository.php';
require __DIR__ . '/../src/OrderManagement/Repositories/OrderRepositoryInterface.php';
require __DIR__ . '/../src/OrderManagement/Repositories/OrderRepository.php';
require __DIR__ . '/../src/OrderManagement/Repositories/DTO/DTOs.php';
require __DIR__ . '/../src/OrderManagement/Entities/Inventory.php';
require __DIR__ . '/../src/OrderManagement/Entities/Warehouse.php';
require __DIR__ . '/../src/OrderManagement/Entities/Order.php';
require __DIR__ . '/../src/OrderManagement/Entities/OrderItem.php';
require __DIR__ . '/../src/OrderManagement/Services/InventoryAllocationService.php';
require __DIR__ . '/../src/OrderManagement/Services/OrderService.php';

use OrderManagement\Repositories\InventoryRepository;
use OrderManagement\Repositories\OrderRepository;
use OrderManagement\Repositories\DTO\ReservationRequest;
use OrderManagement\Services\InventoryAllocationService;
use OrderManagement\Services\OrderService;

// Small helpers to render demo output concisely
function printInventory($inv): void
{
    if (!$inv) { echo "Inventory: not found\n"; return; }
    echo sprintf("Inventory product %d @ WH %d: on_hand=%d reserved=%d\n", $inv->product_id, $inv->warehouse_id, $inv->quantity_on_hand, $inv->quantity_reserved);
}

function printReservationResult($res): void
{
    echo sprintf("Reservation: %s%s - %s\n", $res->success ? 'success' : 'failed', $res->reservationId ? ' (id=' . $res->reservationId . ')' : '', $res->message);
}

function printOrderSummary($order): void
{
    echo sprintf("Order %d status=%s\n", $order['order_id'] ?? 0, $order['status'] ?? '');
}

// Demo prefers SQLite file DB; fall back to in-memory implementation if PDO SQLite missing
$dbFile = __DIR__ . '/demo.sqlite';
if (file_exists($dbFile)) unlink($dbFile);
$drivers = \PDO::getAvailableDrivers();
$useInMemory = false;
if (!in_array('sqlite', $drivers, true)) {
    echo "Using in-memory repositories (sqlite unavailable).\n";
    $useInMemory = true;
} else {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} 




if ($useInMemory) {
    // Use in-memory repositories for demo
    require __DIR__ . '/../src/OrderManagement/Repositories/InMemoryInventoryRepository.php';
    require __DIR__ . '/../src/OrderManagement/Repositories/InMemoryOrderRepository.php';

    $invRepo = new \OrderManagement\Repositories\InMemoryInventoryRepository();
    // seed data
    $invRepo->seedInventory(1, 1, 10, 0);
    $invRepo->seedInventory(1, 2, 5, 0);

    $orderRepo = new \OrderManagement\Repositories\InMemoryOrderRepository();
    $alloc = new InventoryAllocationService($invRepo, [1,2]);
    $orderService = new OrderService($orderRepo, $alloc);
} else {
    $invRepo = new InventoryRepository($pdo);
    $orderRepo = new OrderRepository($pdo);

    // Create minimal tables for demo (SQLite friendly)
    $pdo->exec("CREATE TABLE products (id INTEGER PRIMARY KEY AUTOINCREMENT, sku TEXT, name TEXT, unit_price NUMERIC);");
    $pdo->exec("CREATE TABLE warehouses (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, location TEXT);");
    $pdo->exec("CREATE TABLE inventory (id INTEGER PRIMARY KEY AUTOINCREMENT, product_id INTEGER, warehouse_id INTEGER, quantity_on_hand INTEGER DEFAULT 0, quantity_reserved INTEGER DEFAULT 0, version INTEGER DEFAULT 1);");
    $pdo->exec("CREATE TABLE reservations (id INTEGER PRIMARY KEY AUTOINCREMENT, product_id INTEGER, warehouse_id INTEGER, quantity INTEGER, expires_at TEXT, created_at TEXT, status TEXT);");
    $pdo->exec("CREATE TABLE stock_movements (id INTEGER PRIMARY KEY AUTOINCREMENT, product_id INTEGER, warehouse_id INTEGER, quantity_change INTEGER, movement_type TEXT, reference_id INTEGER, created_at TEXT DEFAULT CURRENT_TIMESTAMP);");
    $pdo->exec("CREATE TABLE orders (id INTEGER PRIMARY KEY AUTOINCREMENT, status TEXT, created_at TEXT DEFAULT CURRENT_TIMESTAMP);");
    $pdo->exec("CREATE TABLE order_items (id INTEGER PRIMARY KEY AUTOINCREMENT, order_id INTEGER, product_id INTEGER, quantity INTEGER, fulfilled_quantity INTEGER DEFAULT 0, backordered INTEGER DEFAULT 0);");

    // Seed data
    $pdo->exec("INSERT INTO products (sku, name, unit_price) VALUES ('SKU-001', 'Widget', 9.99);");
    $pdo->exec("INSERT INTO warehouses (name, location) VALUES ('Main WH', 'NY'), ('Overflow WH', 'NJ');");
    $pdo->exec("INSERT INTO inventory (product_id, warehouse_id, quantity_on_hand, quantity_reserved, version) VALUES (1, 1, 10, 0, 1), (1, 2, 5, 0, 1);");

    $alloc = new InventoryAllocationService($invRepo, [1,2]);
    $orderService = new OrderService($orderRepo, $alloc);
}

echo "=== Demo: Reserve 7 units of product 1 ===\n";
$req = new ReservationRequest(1, 1, 7, 120);
$res = $invRepo->reserveStock($req);
printReservationResult($res);

echo "Inventory after reservation:\n";
printInventory($invRepo->findByProductAndWarehouse(1,1));
printInventory($invRepo->findByProductAndWarehouse(1,2));

echo "\n=== Demo: Release reservation (if any) ===\n";
if ($res->success && $res->reservationId) {
    $invRepo->releaseReservation($res->reservationId);
    echo "Released reservation {$res->reservationId}\n";
}
printInventory($invRepo->findByProductAndWarehouse(1,1));

echo "\n=== Demo: Transfer 3 units from WH1 to WH2 ===\n";
$alloc->transfer(1, 1, 2, 3);
printInventory($invRepo->findByProductAndWarehouse(1,1));
printInventory($invRepo->findByProductAndWarehouse(1,2));

echo "\n=== Demo: Adjust stock (+5) at WH1 ===\n";
$alloc->adjust(1,1,5,'stock take');
printInventory($invRepo->findByProductAndWarehouse(1,1));

echo "\n=== Demo: Create order for 12 units (will partially fulfill/backorder) ===\n";
$order = $orderService->createOrder([['product_id' => 1, 'quantity' => 12]]);
printOrderSummary($order);

echo "\n=== Done ===\n";
