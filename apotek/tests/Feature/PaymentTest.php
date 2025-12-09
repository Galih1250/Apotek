<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);
    }

    /**
     * Test showing payment form
     */
    public function test_payment_form_page_loads()
    {
        $response = $this->actingAs($this->user)->get('/payment');
        
        $response->assertStatus(200);
        $response->assertViewIs('payment.form');
    }

    /**
     * Test creating payment with valid amount
     */
    public function test_create_payment_with_valid_amount()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/payment/create', [
                'amount' => 100000,
                'description' => 'Test Payment',
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'snap_token',
            'order_id',
        ]);

        // Verify transaction was created
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'amount' => 100000,
            'status' => 'pending',
        ]);
    }

    /**
     * Test creating payment with invalid amount
     */
    public function test_create_payment_with_invalid_amount()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/payment/create', [
                'amount' => 5000, // Below minimum
                'description' => 'Test Payment',
            ]);

        $response->assertStatus(422);
    }

    /**
     * Test payment result page
     */
    public function test_payment_result_page()
    {
        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'order_id' => 'ORDER-TEST-123',
            'amount' => 100000,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->user)
            ->get('/payment/result?order_id=' . $transaction->order_id);

        $response->assertStatus(200);
        $response->assertViewIs('payment.result');
        $response->assertViewHas('transaction');
    }

    /**
     * Test payment history
     */
    public function test_payment_history()
    {
        // Create multiple transactions
        Transaction::factory(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get('/payment/history');

        $response->assertStatus(200);
        $response->assertViewIs('payment.history');
        $response->assertViewHas('transactions');
    }

    /**
     * Test checking payment status
     */
    public function test_check_payment_status()
    {
        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'order_id' => 'ORDER-TEST-456',
            'amount' => 100000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson('/payment/check-status', [
                'order_id' => $transaction->order_id,
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'transaction_status',
            'local_status',
        ]);
    }

    /**
     * Test payment requires authentication
     */
    public function test_payment_requires_authentication()
    {
        $response = $this->get('/payment');
        $response->assertRedirect('/login');
    }

    /**
     * Test transaction model relationships
     */
    public function test_transaction_relationships()
    {
        $transaction = Transaction::create([
            'user_id' => $this->user->id,
            'order_id' => 'ORDER-TEST-789',
            'amount' => 100000,
            'status' => 'completed',
        ]);

        $this->assertTrue($transaction->user()->exists());
        $this->assertEquals($transaction->user->id, $this->user->id);
    }

    /**
     * Test transaction status helpers
     */
    public function test_transaction_status_helpers()
    {
        $pending = Transaction::create([
            'user_id' => $this->user->id,
            'order_id' => 'ORDER-PENDING',
            'amount' => 100000,
            'status' => 'pending',
        ]);

        $completed = Transaction::create([
            'user_id' => $this->user->id,
            'order_id' => 'ORDER-COMPLETED',
            'amount' => 100000,
            'status' => 'completed',
        ]);

        $failed = Transaction::create([
            'user_id' => $this->user->id,
            'order_id' => 'ORDER-FAILED',
            'amount' => 100000,
            'status' => 'failed',
        ]);

        $this->assertTrue($pending->isPending());
        $this->assertTrue($completed->isCompleted());
        $this->assertTrue($failed->isFailed());
    }
}
