<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Mail\NewContactMail;
use App\Models\ContactMessage;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('store.pages.contact');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', function (string $attribute, mixed $value, \Closure $fail) {
                if (! Customer::isValidVietnamesePhone((string) $value)) {
                    $fail('Số điện thoại phải có 10 chữ số (Việt Nam).');
                }
            }],
            'email' => ['nullable', 'email', 'max:255'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $validated['phone'] = Customer::normalizePhone($validated['phone']);

        $message = ContactMessage::query()->create($validated);

        $storeEmail = store_setting('email');

        if (filled($storeEmail)) {
            try {
                Mail::to($storeEmail)->queue(new NewContactMail($message));
            } catch (\Throwable $e) {
                Log::error('Failed to queue contact notification.', [
                    'contact_message_id' => $message->id,
                    'email' => $storeEmail,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return back()->with('success', 'Cảm ơn bạn! Chúng tôi đã nhận tin nhắn và sẽ phản hồi sớm nhất.');
    }
}
