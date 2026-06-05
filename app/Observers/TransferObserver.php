<?php

namespace App\Observers;

use App\Models\Members;
use App\Models\Transfer;
use Illuminate\Support\Facades\Log;
use NotificationChannels\Telegram\TelegramMessage;

class TransferObserver
{
    public function created(Transfer $transfer): void
    {
        if ($transfer->type !== 'withdraw') {
            return;
        }

        $chatId = env('TELEGRAM_G_ID');

        if (empty($chatId)) {
            Log::warning('Telegram withdraw notify skipped: TELEGRAM_G_ID is not configured.', [
                'transfer_id' => $transfer->id,
            ]);
            return;
        }

        $member = Members::find($transfer->member_id);

        try {
            TelegramMessage::create()
                ->to($chatId)
                ->line('BOT ' . env('APP_NAME'))
                ->line('New withdrawal request')
                ->line('Transfer ID: ' . $transfer->id)
                ->line('User: ' . ($member->username ?? '-'))
                ->line('Amount: ' . number_format((float) $transfer->amount, 2))
                ->line('Bank: ' . ($transfer->deposit_to_bank_type ?? '-'))
                ->line('Account No: ' . ($transfer->deposit_to_bank_no ?? '-'))
                ->line('Account Name: ' . ($transfer->deposit_to_bank_name ?? '-'))
                ->line('Status: ' . ($transfer->status_code ?? $transfer->status ?? '-'))
                ->send();
        } catch (\Throwable $e) {
            Log::error('Telegram notify error (withdraw created): ' . $e->getMessage(), [
                'transfer_id' => $transfer->id,
            ]);
        }
    }
}
