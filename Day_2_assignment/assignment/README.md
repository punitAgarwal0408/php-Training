# Order Management System (Assignment 2)

This workspace contains a simple PHP implementation of an order management system handling multi-warehouse inventory, reservations, transfers, adjustments and an audit trail.

Files added:

- `sql/schema.sql` - MySQL-compatible database schema with indexes and audit tables
- `src/OrderManagement/Repositories` - repository implementations (`InventoryRepository`, `OrderRepository`) and DTOs
- `src/OrderManagement/Entities` - domain entities (`Inventory`, `Warehouse`, `Order`, `OrderItem`)
- `src/OrderManagement/Services` - `InventoryAllocationService`, `OrderService`
- `demo/order_demo.php` - demo script using an SQLite file DB

Quick demo:

1. Run `php demo/order_demo.php` to see reservation, release, transfer, adjustment and order creation flows.

Notes & next steps:
- The demo uses an SQLite DB for convenience. The provided `sql/schema.sql` is MySQL-ready and should be used for production/MySQL.
- Inventory allocation is simplified; you may want to extend `InventoryAllocationService` to query live warehouses and distribute allocation across them intelligently.
- Optimistic locking is implemented via the `version` column in `inventory` and checked during updates.

If you'd like, I can:
- Add unit tests / integration tests
- Implement a REST API front-end
- Add more robust reservation cleanup (expiry worker)
- Improve allocation algorithm and add warehouse inventory listing

