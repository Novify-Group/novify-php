<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine if the user can view the order
     */
    public function view(User $user, Order $order): bool
    {
        return $user->merchant_id === $order->merchant_id;
    }

    /**
     * Determine if the user can create orders
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create orders
    }
} 