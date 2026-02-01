<?php

namespace OrderManagement\Repositories;

class InMemoryOrderRepository implements OrderRepositoryInterface
{
    private array $orders = [];
    private array $orderItems = [];
    private int $orderSeq = 1;
    private int $itemSeq = 1;

    public function createOrder(string $status = 'created'): int
    {
        $id = $this->orderSeq++;
        $this->orders[$id] = ['id' => $id, 'status' => $status, 'created_at' => (new \DateTime())->format('c')];
        return $id;
    }

    public function addItem(int $orderId, int $productId, int $quantity): int
    {
        $id = $this->itemSeq++;
        $this->orderItems[$id] = ['id' => $id, 'order_id' => $orderId, 'product_id' => $productId, 'quantity' => $quantity, 'fulfilled_quantity' => 0, 'backordered' => false];
        return $id;
    }

    public function setOrderStatus(int $orderId, string $status): void
    {
        if (!isset($this->orders[$orderId])) throw new \RuntimeException('Order not found');
        $this->orders[$orderId]['status'] = $status;
    }

    public function getOrder(int $orderId): array
    {
        return $this->orders[$orderId] ?? [];
    }
}
