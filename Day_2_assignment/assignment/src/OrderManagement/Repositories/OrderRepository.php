<?php

namespace OrderManagement\Repositories;

use PDO;

class OrderRepository implements OrderRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createOrder(string $status = 'created'): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO orders (status) VALUES (?)');
        $stmt->execute([$status]);
        return (int)$this->pdo->lastInsertId();
    }

    public function addItem(int $orderId, int $productId, int $quantity): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)');
        $stmt->execute([$orderId, $productId, $quantity]);
        return (int)$this->pdo->lastInsertId();
    }

    public function setOrderStatus(int $orderId, string $status): void
    {
        $stmt = $this->pdo->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$status, $orderId]);
    }
}
