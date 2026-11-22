<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Services;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_services_list(): void
    {
        Services::create([
            'service_name' => 'Cuci Kiloan Regular',
            'category' => 'Kiloan',
            'price_per_unit' => 7000,
            'unit_type' => 'Kg',
            'description' => 'Cuci bersih dan wangi.',
        ]);

        $response = $this->get(route('customer.index'));

        $response->assertStatus(200);
        $response->assertSee('Cuci Kiloan Regular');
        $response->assertSee('7.000');
    }

    public function test_customer_can_place_order_with_qty_or_weight(): void
    {
        $service = Services::create([
            'service_name' => 'Cuci Express',
            'category' => 'Express',
            'price_per_unit' => 15000,
            'unit_type' => 'Kg',
            'description' => 'Express 6 Jam',
        ]);

        $response = $this->post(route('customer.checkout'), [
            'customer_name' => 'Budi Santoso',
            'whatsapp_number' => '081234567890',
            'items' => [
                $service->id => [
                    'service_id' => $service->id,
                    'qty_or_weight' => 3,
                ],
            ],
        ]);

        $response->assertRedirect(route('customer.index'));
        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Budi Santoso',
            'whatsapp_number' => '081234567890',
            'total_price' => 45000,
            'status' => 'Antrean',
        ]);

        $order = Order::first();
        $this->assertDatabaseHas('order_details', [
            'order_id' => $order->id,
            'service_id' => $service->id,
            'qty_or_weight' => 3,
            'subtotal' => 45000,
        ]);
    }

    public function test_admin_can_access_dashboard_and_update_order_status(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $order = Order::create([
            'customer_name' => 'Siti Nurhaliza',
            'whatsapp_number' => '08987654321',
            'total_price' => 20000,
            'status' => 'Antrean',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Siti Nurhaliza');

        $patchResponse = $this->actingAs($admin)->patch(route('admin.orders.updateStatus', $order), [
            'status' => 'Dicuci',
        ]);

        $patchResponse->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'Dicuci',
        ]);
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_USER,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(403);
    }
}
