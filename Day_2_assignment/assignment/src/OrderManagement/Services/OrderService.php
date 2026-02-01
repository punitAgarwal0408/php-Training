<?php

namespace OrderManagement\Services;

use OrderManagement\Repositories\OrderRepositoryInterface;

class OrderService
{
    private OrderRepositoryInterface $orderRepo;
    private InventoryAllocationService $allocationService;

    public function __construct(OrderRepositoryInterface $orderRepo, InventoryAllocationService $allocationService)
    {
        $this->orderRepo = $orderRepo;
        $this->allocationService = $allocationService;
    }

    public function createOrder(array $items): array
    {
        $orderId = $this->orderRepo->createOrder('created');
        foreach ($items as $it) {
            $this->orderRepo->addItem($orderId, $it['product_id'], $it['quantity']);
        }

        $allFulfilled = true;
        $anyFulfilled = false;
        $allocations = [];

        foreach ($items as $it) {
            $res = $this->allocationService->allocate($it['product_id'], $it['quantity']);
            $allocations[$it['product_id']] = $res;
            if ($res['remaining'] === 0) {
                $anyFulfilled = true;
            } else {
                $allFulfilled = false;
            }
        }

        if ($allFulfilled) $status = 'fulfilled';
        elseif ($anyFulfilled) $status = 'partially_fulfilled';
        else $status = 'backordered';

        $this->orderRepo->setOrderStatus($orderId, $status);

        return ['order_id' => $orderId, 'status' => $status, 'allocations' => $allocations];
    }
}
