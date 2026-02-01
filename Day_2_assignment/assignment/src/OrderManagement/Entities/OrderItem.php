<?php

namespace OrderManagement\Entities;

class OrderItem
{
    public int $id;
    public int $order_id;
    public int $product_id;
    public int $quantity;
    public int $fulfilled_quantity;
    public bool $backordered;

    public function __construct(array $data)
    {
        $this->id = (int)($data['id'] ?? 0);
        $this->order_id = (int)($data['order_id'] ?? 0);
        $this->product_id = (int)$data['product_id'];
        $this->quantity = (int)$data['quantity'];
        $this->fulfilled_quantity = (int)($data['fulfilled_quantity'] ?? 0);
        $this->backordered = (bool)($data['backordered'] ?? false);
    }
}
