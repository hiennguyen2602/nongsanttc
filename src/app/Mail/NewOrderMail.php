<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Đơn hàng mới ' . $this->order->order_code . ' - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.orders.new',
            with: [
                'orderUrl' => route('admin.orders.show', $this->order, absolute: true),
            ],
        );
    }
}
