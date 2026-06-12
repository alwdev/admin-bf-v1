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
        $this->notifyWithdraw($transfer, 'created');
    }

    public function updated(Transfer $transfer): void
    {
        if ($transfer->type !== 'withdraw') {
            return;
        }

        $statusChangedToPending = $transfer->wasChanged('status') && (int) $transfer->status === 1;
        $statusCodeChangedToPending = $transfer->wasChanged('status_code')
            && in_array($transfer->status_code, ['รออนุมัติ', 'รอดำเนินการ'], true);

        if (!$statusChangedToPending && !$statusCodeChangedToPending) {
            return;
        }

        $this->notifyWithdraw($transfer, 'updated');
    }

    private function notifyWithdraw(Transfer $transfer, string $event): void
    {
        if ($transfer->type !== 'withdraw') {
            return;
        }

        $chatId = env('TELEGRAM_G_ID');

        if (empty($chatId)) {
            Log::warning('Telegram withdraw notify skipped: TELEGRAM_G_ID is not configured.', [
                'transfer_id' => $transfer->id,
                'event' => $event,
            ]);
            return;
        }

        $member = Members::find($transfer->member_id);
        $bankName = $transfer->withdraw_bank_type
            ?? $transfer->withdraw_bank_name
            ?? $transfer->deposit_to_bank_type
            ?? $transfer->deposit_to_bank_name
            ?? ($member->bank_name ?? '-');
        $accountNo = $transfer->withdraw_bank_no
            ?? $transfer->deposit_to_bank_no
            ?? ($member->bank_number ?? '-');
        $accountName = $transfer->withdraw_bank_account_name
            ?? $transfer->withdraw_bank_name
            ?? $transfer->deposit_to_bank_name
            ?? ($member->account_name ?? '-');

        try {
            TelegramMessage::create()
                ->to($chatId)
                ->line('BOT ' . env('APP_NAME'))
                ->line('New withdrawal request')
                ->line('Transfer ID: ' . $transfer->id)
                ->line('User: ' . ($member->username ?? '-'))
                ->line('Amount: ' . number_format((float) $transfer->amount, 2))
                ->line('Bank: ' . $bankName)
                ->line('Account No: ' . $accountNo)
                ->line('Account Name: ' . $accountName)
                ->line('Status: ' . ($transfer->status_code ?? $transfer->status ?? '-'))
                ->send();
        } catch (\Throwable $e) {
            Log::error('Telegram notify error (withdraw ' . $event . '): ' . $e->getMessage(), [
                'transfer_id' => $transfer->id,
                'event' => $event,
            ]);
        }
    }
}
