<?php

namespace Database\Seeders;

use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        $promotions = [
            ['code' => 'TTC001', 'title' => 'Miễn phí vận chuyển', 'description' => 'Đơn hàng từ 350k', 'min_order' => 350000, 'discount_amount' => 30000],
            ['code' => 'TTC002', 'title' => 'Giảm 5k', 'description' => 'Đơn hàng từ 100k', 'min_order' => 100000, 'discount_amount' => 5000],
            ['code' => 'TTC003', 'title' => 'Giảm 10k', 'description' => 'Đơn hàng từ 200k', 'min_order' => 200000, 'discount_amount' => 10000],
            ['code' => 'TTC004', 'title' => 'Giảm 30k', 'description' => 'Đơn hàng từ 500k', 'min_order' => 500000, 'discount_amount' => 30000],
        ];

        foreach ($promotions as $promo) {
            Promotion::query()->updateOrCreate(['code' => $promo['code']], $promo);
        }
    }
}
