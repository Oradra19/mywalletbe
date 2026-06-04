<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransferService
{
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function transfer(User $sender, array $data): array
    {
        if ($sender->id === (int) $data['receiver_id']) {
            return [
                'success' => false,
                'message' => 'Cannot transfer to yourself',
            ];
        }

        return DB::transaction(function () use ($sender, $data): array {
    $lockedSender = User::whereKey($sender->id)->lockForUpdate()->first();
    $receiver = User::whereKey($data['receiver_id'])->lockForUpdate()->first();
    $amount = (float) $data['amount'];

    if ($amount <= 0) {
        return [
            'success' => false,
            'message' => 'Amount must be greater than zero',
        ];
    }

    if (!$lockedSender || !$receiver) {
        return [
            'success' => false,
            'message' => 'Recipient user not found',
        ];
    }

    if ((float) $lockedSender->balance < $amount) {
        return [
            'success' => false,
            'message' => 'Insufficient balance',
        ];
    }

    $lockedSender->balance -= $amount;
    $receiver->balance += $amount;

    $lockedSender->save();
    $receiver->save();

    $transaction = Transaction::create([
        'transaction_code' => $this->generateTransactionCode(),
        'sender_id' => $lockedSender->id,
        'receiver_id' => $receiver->id,
        'amount' => $amount,
        'transaction_type' => 'transfer',
    ]);

    return [
        'success' => true,
        'message' => 'Transfer successful',
        'data' => [
            'transaction_code' => $transaction->transaction_code,
            'sender_balance' => (float) $lockedSender->balance,
            'receiver_id' => $receiver->id,
            'amount' => $amount,
        ],
    ];
});
    }

    /**
     * @return array<string, mixed>
     */
    public function history(User $user): array
{
    $transactions = Transaction::query()
        ->with(['sender:id,name', 'receiver:id,name'])
        ->where('sender_id', $user->id)
        ->orWhere('receiver_id', $user->id)
        ->latest()
        ->paginate(10);

    return [
        'success' => true,
        'message' => 'Transactions retrieved successfully',
        'data' => $transactions->getCollection()
            ->map(fn (Transaction $transaction): array => [
                'transaction_code' => $transaction->transaction_code,
                'sender_id' => $transaction->sender_id,
                'receiver_id' => $transaction->receiver_id,
                'sender_name' => $transaction->sender?->name,
                'receiver_name' => $transaction->receiver?->name,
                'amount' => (float) $transaction->amount,
                'transaction_type' => $transaction->transaction_type,
                'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
            ])
            ->values()
            ->all(),
        'meta' => [
            'current_page' => $transactions->currentPage(),
            'last_page' => $transactions->lastPage(),
            'total' => $transactions->total(),
            'per_page' => $transactions->perPage(),
        ],
    ];
}

    private function generateTransactionCode(): string
    {
        do {
            $code = 'TRX-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Transaction::where('transaction_code', $code)->exists());

        return $code;
    }
}
