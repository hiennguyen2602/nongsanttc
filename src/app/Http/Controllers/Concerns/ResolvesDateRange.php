<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

trait ResolvesDateRange
{
    /**
     * Xác định period từ request: chỉ dùng custom khi người dùng nhập ngày.
     */
    protected function resolveFilterPeriod(Request $request): string
    {
        if ($request->filled('from') || $request->filled('to')) {
            return 'custom';
        }

        return $request->input('period', 'recent');
    }

    /**
     * Quy đổi mã khoảng thời gian thành cặp [from, to].
     *
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    protected function resolveDateRange(string $period, Request $request): array
    {
        $now = now();
        $todayEnd = $now->copy()->endOfDay();

        [$fromDate, $toDate] = match ($period) {
            'today' => [$now->copy()->startOfDay(), $todayEnd],
            'week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'all' => [null, null],
            'custom' => $this->resolveCustomDateRange($request, $now, $todayEnd),
            // Mặc định: 1 tháng gần nhất → hôm nay
            default => [$now->copy()->subMonth()->startOfDay(), $todayEnd],
        };

        if ($toDate?->gt($todayEnd)) {
            $toDate = $todayEnd;
        }

        if ($fromDate && $toDate && $fromDate->gt($toDate)) {
            $fromDate = $toDate->copy()->startOfDay();
        }

        return [$fromDate, $toDate];
    }

    /**
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    private function resolveCustomDateRange(Request $request, Carbon $now, Carbon $todayEnd): array
    {
        if (! $request->filled('from') && ! $request->filled('to')) {
            return [null, null];
        }

        return [
            $request->filled('from') ? Carbon::parse($request->input('from'))->startOfDay() : $now->copy()->subMonth()->startOfDay(),
            $request->filled('to') ? Carbon::parse($request->input('to'))->endOfDay() : $todayEnd,
        ];
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
