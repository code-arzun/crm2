<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_bisa_memverifikasi_pembayaran()
    {
        // 1. Setup Role & User Finance
        Role::create(['name' => 'finance']);
        $finance = User::factory()->create();
        $finance->assignRole('finance');

        // 2. Setup Order
        Role::create(['name' => 'customer']);
        $customerUser = User::factory()->create();
        $customerUser->assignRole('customer');
        
        $customer = Customer::create([
            'user_id' => $customerUser->id,
            'nama_lengkap' => 'Budi Test',
            'telepon' => '08123456789',
        ]);

        $order = Order::create([
            'customer_id' => $customer->id,
            'kode_order' => 'ORD-TEST-01',
            'status' => 'dikonfirmasi',
            'payment_status' => 'unpaid',
            'total' => 500000,
            'subtotal' => 500000,
            'jalur' => 'self_order',
            'payment_type' => 'transfer',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'metode' => 'transfer',
            'jumlah' => 500000,
            'status' => 'pending',
            'bukti_transfer' => 'dummy.jpg',
        ]);

        // 3. Aksi: Finance verifikasi via Livewire
        \Livewire\Livewire::actingAs($finance)
            ->test(\App\Livewire\Payments\VerifyIndex::class)
            ->call('openPayModal', $order->id)
            ->call('verify', $payment->id);

        // 4. Assert
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'verified',
            'verified_by' => $finance->id,
        ]);

        // Pastikan status order berubah jadi paid
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'paid',
        ]);
    }
}