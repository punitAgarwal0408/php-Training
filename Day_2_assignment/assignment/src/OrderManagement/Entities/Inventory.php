<?php

namespace OrderManagement\Entities;

class Inventory
{
    public int $id;
    public int $product_id;
    public int $warehouse_id;
    public int $quantity_on_hand;
    public int $quantity_reserved;
    public int $version;

    public function __construct(array $data)
    {
        $this->id = (int)($data['id'] ?? 0);
        $this->product_id = (int)$data['product_id'];
        $this->warehouse_id = (int)$data['warehouse_id'];
        $this->quantity_on_hand = (int)($data['quantity_on_hand'] ?? 0);
        $this->quantity_reserved = (int)($data['quantity_reserved'] ?? 0);
        $this->version = (int)($data['version'] ?? 1);
    }
}
