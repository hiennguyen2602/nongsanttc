<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

trait ResolvesDateRange
{
    /**
     * Quy đổi mã khoảng thời gian thành cặp [from, to].
     *
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    protected function resolveDateRange(string $period, Request $request): array
    {
        $now = now();

        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'all' => [null, null],
            'custom' => [
                $request->filled('from') ? Carbon::parse($request->input('from'))->startOfDay() : $now->copy()->subMonth()->startOfDay(),
                $request->filled('to') ? Carbon::parse($request->input('to'))->endOfDay() : $now->copy()->endOfDay(),
            ],
            // Mặc định: 1 tháng gần nhất → hôm nay
            default => [$now->copy()->subMonth()->startOfDay(), $now->copy()->endOfDay()],
        };
    }

    /**
     * Danh sách nhãn cho các nút lọc nhanh.
     *
     * @return array<string, string>
     */
    protected function datePeriods(): array
    {
        return [
            'today' => 'Hôm nay',
            'week' => 'Tuần này',
            'month' => 'Tháng này',
            'year' => 'Từ đầu năm',
            'all' => 'Tất cả',
        ];
    }
}
