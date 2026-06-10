<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const TYPE_ADMIN = 1;

    public const TYPE_STAFF = 2;

    public const TYPE_USER = 3;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'type' => 'integer',
            'status' => 'integer',
        ];
    }

    public function isAdmin(): bool
    {
        return (int) $this->type === self::TYPE_ADMIN;
    }

    public function isStaff(): bool
    {
        return (int) $this->type === self::TYPE_STAFF;
    }

    public function isUser(): bool
    {
        return (int) $this->type === self::TYPE_USER;
    }

    public function canAccessAdminPanel(): bool
    {
        return $this->isAdmin() || $this->isStaff();
    }

    public function isActive(): bool
    {
        return (int) $this->status === 1;
    }

    public static function adminRoleLabels(): array
    {
        return [
            self::TYPE_ADMIN => 'Quản trị viên',
            self::TYPE_STAFF => 'Nhân viên',
        ];
    }

    public static function roleLabels(): array
    {
        return self::adminRoleLabels() + [
            self::TYPE_USER => 'Khách hàng',
        ];
    }

    public function roleLabel(): string
    {
        return self::roleLabels()[(int) $this->type] ?? '—';
    }

    /** @return array{active: string, inactive: string} */
    public static function accountStatusLabels(): array
    {
        return config('labels.account');
    }

    public function accountStatusLabel(): string
    {
        return self::accountStatusLabels()[(int) $this->status] ?? '—';
    }

    public function accountStatusBadgeClass(): string
    {
        return $this->isActive() ? 'badge-success' : 'badge-secondary';
    }
}
