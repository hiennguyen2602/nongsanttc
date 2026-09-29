<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    /**
     * Tìm hoặc tạo khách hàng theo SĐT (unique), cập nhật thông tin mới nhất.
     */
    public function resolveFromCheckout(array $data): Customer
    {
        $phone = Customer::normalizePhone($data['phone']);

        $customer = Customer::query()->firstOrNew(['phone' => $phone]);
        $customer->fill([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'address' => $data['address'],
        ]);
        $customer->save();

        return $customer;
    }
}
