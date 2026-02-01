<?php

namespace OrderManagement\Repositories;

interface OrderRepositoryInterface
{
    public function createOrder(string $status = 'created'): int;

    public function addItem(int $orderId, int $productId, int $quantity): int;

    public function setOrderStatus(int $orderId, string $status): void;
}
