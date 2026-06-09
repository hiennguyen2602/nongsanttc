<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_SHIPPING = 'shipping';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'order_code',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'note',
        'subtotal',
        'shipping_fee',
        'discount',
        'total',
        'promo_code',
        'payment_method',
        'status',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'Chờ xử lý',
            self::STATUS_CONFIRMED => 'Đã xác nhận',
            self::STATUS_SHIPPING => 'Đang giao',
            self::STATUS_COMPLETED => 'Hoàn thành',
            self::STATUS_CANCELLED => 'Đã hủy',
        ];
    }

    /**
     * Màu đại diện cho từng trạng thái (dùng cho số liệu thống kê).
     *
     * @return array<string, string>
     */
    public static function statusColors(): array
    {
        return [
            self::STATUS_PENDING => 'orange',
            self::STATUS_CONFIRMED => 'blue',
            self::STATUS_SHIPPING => 'fuchsia',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_CANCELLED => 'red',
        ];
    }

    public function statusColor(): string
    {
        return self::statusColors()[$this->status] ?? '#64748b';
    }

    public function statusBadgeClass(): string
    {
        return 'badge-status-' . $this->status;
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function formattedTotal(): string
    {
        return number_format($this->total, 0, ',', '.') . 'đ';
    }
}
