<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Transaction;
use App\ExternalServices\Notifier;
use App\ExternalServices\Authorizer;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Requests\UpdateTransactionRequest;


class TransactionControllerTest extends TestCase
{
    
    use RefreshDatabase;

    private $authorizer;
    private $notifier;
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authorizer = $this->createMock(Authorizer::class);
        $this->notifier = $this->createMock(Notifier::class);
        $this->controller = new TransactionController($this->authorizer, $this->notifier);
    }

    public function test_index()
    {
        $payee = User::factory()->create();
        $payer = User::factory()->create();
 
        $transaction = Transaction::factory()->create([
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 1.00,
        ]);
  
        $response = $this->getJson('/api/transactions');
   
        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'data' => [
                [
                    'uuid' => $transaction->uuid,
                    'payer' => $transaction->payer,
                    'payee' => $transaction->payee,
                    'value' => $transaction->value,
                    'payer_user' => [
                        'id' => $payer->id,
                        'name' => $payer->name,
                        'email' => $payer->email,
                        'document' => $payer->document,
                        'wallet' => $payer->wallet,
                    ],
                    'payee_user' => [
                        'id' => $payee->id,
                        'name' => $payee->name,
                        'email' => $payee->email,
                        'document' => $payee->document,
                        'wallet' => $payee->wallet,
                    ],
                ]
            ]
        ]);
    }

    public function test_store_with_valid_data()
    {
        $payer = User::factory()->create(['wallet' => 100, 'type' => 'client']);
        $payee = User::factory()->create(['wallet' => 0, 'type' => 'seller']);
        $request = new StoreTransactionRequest([
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 50,
        ]);

        // $this->authorizer->expects($this->once())
        //     ->method('authorizeTransaction')
        //     ->willReturn(true);

        // $this->notifier->expects($this->once())
        //     ->method('notification')
        //     ->willReturn(true);

        $response = $this->postJson('/api/transfer', $request->all());

        $response->assertOk();
        $this->assertDatabaseHas('transactions', [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => $request->value,
        ]);

        $this->assertEquals(50, $payer->fresh()->wallet);
        $this->assertEquals(50, $payee->fresh()->wallet);
    }

    public function test_store_with_invalid_data()
    {
        $payer = User::factory()->create(['wallet' => 100]);
        $payee = User::factory()->create(['wallet' => 0]);
        $request = new StoreTransactionRequest([
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 150,
        ]);

        // $this->authorizer->expects($this->once())
        //     ->method('authorizeTransaction')
        //     ->willReturn(true);
 
       
        $response = $this->postJson('/api/transfer', $request->all());

        $response->assertStatus(401);
        $this->assertDatabaseMissing('transactions', [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => $request->value,
        ]);
        $this->assertEquals(100, $payer->fresh()->wallet);
        $this->assertEquals(0, $payee->fresh()->wallet);
    }

    public function test_reverse()
    {
        $payee = User::factory()->create(['wallet' => 1, 'type' => 'seller']);
        $payer = User::factory()->create(['wallet' => 0, 'type' => 'client']);
 
        $transaction = Transaction::factory()->create([
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 1.00,
        ]);

        $request = new UpdateTransactionRequest([
            'uuid' => $transaction->uuid
        ]);

        $response = $this->postJson('/api/reverse', $request->all());

        $response->assertOk();

        $this->assertDatabaseHas('transactions', [
            'uuid' => $transaction->uuid,
            'was_reversed' => true
        ]);

        $this->assertEquals(1, $payer->fresh()->wallet);
        $this->assertEquals(0, $payee->fresh()->wallet);

    }

 
 
 
}