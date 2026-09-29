<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StoreContactRequest;
use App\Mail\NewContactMail;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('store.pages.contact');
    }

    public function store(StoreContactRequest $request): RedirectResponse
    {
        $message = ContactMessage::query()->create($request->validated());

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
