<?php

namespace OrderManagement\Entities;

class Order
{
    public int $id;
    public string $status;
    public string $created_at;
    public array $items = [];

    public function __construct(array $data)
    {
        $this->id = (int)($data['id'] ?? 0);
        $this->status = $data['status'] ?? 'created';
        $this->created_at = $data['created_at'] ?? date('c');
    }
}
