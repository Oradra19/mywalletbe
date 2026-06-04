<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_only_see_their_own_transactions_latest_first(): void
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();
        $otherUser = User::factory()->create();

        $olderTransaction = Transaction::create([
            'transaction_code' => 'TRX-20260605-AAAAAA',
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'amount' => 10000,
            'transaction_type' => 'transfer',
        ]);
        $olderTransaction->created_at = now()->subDay();
        $olderTransaction->updated_at = now()->subDay();
        $olderTransaction->save();

        $latestTransaction = Transaction::create([
            'transaction_code' => 'TRX-20260605-BBBBBB',
            'sender_id' => $receiver->id,
            'receiver_id' => $user->id,
            'amount' => 20000,
            'transaction_type' => 'transfer',
        ]);
        $latestTransaction->created_at = now();
        $latestTransaction->updated_at = now();
        $latestTransaction->save();

        Transaction::create([
            'transaction_code' => 'TRX-20260605-CCCCCC',
            'sender_id' => $receiver->id,
            'receiver_id' => $otherUser->id,
            'amount' => 30000,
            'transaction_type' => 'transfer',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/transactions');

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Transactions retrieved successfully',
                'data' => [
                    [
                        'sender_name' => $receiver->name,
                        'receiver_name' => $user->name,
                    ],
                    [
                        'sender_name' => $user->name,
                        'receiver_name' => $receiver->name,
                    ],
                ],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 2,
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    [
                        'transaction_code',
                        'sender_id',
                        'receiver_id',
                        'sender_name',
                        'receiver_name',
                        'amount',
                        'transaction_type',
                        'created_at',
                    ],
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'total',
                ],
            ]);

        $this->assertSame($latestTransaction->transaction_code, $response->json('data.0.transaction_code'));
        $this->assertSame($olderTransaction->transaction_code, $response->json('data.1.transaction_code'));
        $this->assertCount(2, $response->json('data'));
    }

    public function test_transaction_history_is_paginated_ten_per_page(): void
    {
        $user = User::factory()->create();
        $receiver = User::factory()->create();

        foreach (range(1, 11) as $index) {
            $transaction = Transaction::create([
                'transaction_code' => 'TRX-20260605-' . str_pad((string) $index, 6, '0', STR_PAD_LEFT),
                'sender_id' => $user->id,
                'receiver_id' => $receiver->id,
                'amount' => 10000 + $index,
                'transaction_type' => 'transfer',
            ]);
            $transaction->created_at = now()->addSeconds($index);
            $transaction->updated_at = now()->addSeconds($index);
            $transaction->save();
        }

        Sanctum::actingAs($user);

        $this->getJson('/api/transactions')
            ->assertOk()
            ->assertJson([
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 2,
                    'total' => 11,
                ],
            ])
            ->assertJsonCount(10, 'data');
    }
}
