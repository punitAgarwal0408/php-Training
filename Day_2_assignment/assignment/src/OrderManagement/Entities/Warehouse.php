<?php

namespace OrderManagement\Entities;

class Warehouse
{
    public int $id;
    public string $name;
    public ?string $location;

    public function __construct(array $data)
    {
        $this->id = (int)($data['id'] ?? 0);
        $this->name = $data['name'];
        $this->location = $data['location'] ?? null;
    }
}
