<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_transfer_money(): void
    {
        $sender = User::factory()->create([
            'balance' => 100000,
        ]);
        $receiver = User::factory()->create([
            'balance' => 50000,
        ]);

        Sanctum::actingAs($sender);

        $response = $this->postJson('/api/transfer', [
            'receiver_id' => $receiver->id,
            'amount' => 10000,
        ]);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Transfer successful',
                'data' => [
                    'sender_balance' => 90000,
                    'receiver_id' => $receiver->id,
                    'amount' => 10000,
                ],
            ])
            ->assertJsonStructure([
                'data' => [
                    'transaction_code',
                    'sender_balance',
                    'receiver_id',
                    'amount',
                ],
            ]);

        $this->assertSame('90000.00', $sender->fresh()->balance);
        $this->assertSame('60000.00', $receiver->fresh()->balance);
        $this->assertDatabaseHas('transactions', [
            'transaction_code' => $response->json('data.transaction_code'),
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'amount' => 10000,
            'transaction_type' => 'transfer',
        ]);
    }

    public function test_transfer_requires_authentication(): void
    {
        $receiver = User::factory()->create();

        $this->postJson('/api/transfer', [
            'receiver_id' => $receiver->id,
            'amount' => 10000,
        ])->assertUnauthorized();
    }

    public function test_transfer_requires_positive_numeric_amount(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();

        Sanctum::actingAs($sender);

        $this->postJson('/api/transfer', [
            'receiver_id' => $receiver->id,
            'amount' => 0,
        ])->assertUnprocessable();
    }

    public function test_user_cannot_transfer_to_themselves(): void
    {
        $user = User::factory()->create([
            'balance' => 100000,
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/transfer', [
            'receiver_id' => $user->id,
            'amount' => 10000,
        ])
            ->assertUnprocessable()
            ->assertExactJson([
                'success' => false,
                'message' => 'Cannot transfer to yourself',
            ]);

        $this->assertSame('100000.00', $user->fresh()->balance);
        $this->assertSame(0, Transaction::count());
    }

    public function test_user_cannot_transfer_more_than_their_balance(): void
    {
        $sender = User::factory()->create([
            'balance' => 5000,
        ]);
        $receiver = User::factory()->create([
            'balance' => 50000,
        ]);

        Sanctum::actingAs($sender);

        $this->postJson('/api/transfer', [
            'receiver_id' => $receiver->id,
            'amount' => 10000,
        ])
            ->assertUnprocessable()
            ->assertExactJson([
                'success' => false,
                'message' => 'Insufficient balance',
            ]);

        $this->assertSame('5000.00', $sender->fresh()->balance);
        $this->assertSame('50000.00', $receiver->fresh()->balance);
        $this->assertSame(0, Transaction::count());
    }
}
