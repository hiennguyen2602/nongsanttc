<?php

namespace App\Rules;

use App\Models\Customer;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidVietnamesePhone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Customer::isValidVietnamesePhone((string) $value)) {
            $fail('Số điện thoại phải có 10 chữ số (Việt Nam).');
        }
    }
}
