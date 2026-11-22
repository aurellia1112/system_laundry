<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Services;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $services = Services::query()->orderBy('service_name')->get();

        return view('customer.index', compact('services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.service_id' => 'required|exists:services,id',
            'items.*.quantity' => 'nullable|integer|min:0',
            'items.*.qty_or_weight' => 'nullable|integer|min:0',
        ]);

        $items = collect($validated['items'])
            ->map(function (array $item): array {
                $item['qty_or_weight'] = (int) ($item['qty_or_weight'] ?? $item['quantity'] ?? 0);

                return $item;
            })
            ->filter(fn (array $item): bool => $item['qty_or_weight'] > 0);

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Pilih minimal satu layanan dengan jumlah lebih dari nol.',
            ]);
        }

        DB::transaction(function () use ($validated, $items): void {
            $serviceIds = $items->pluck('service_id')->unique();
            $services = Services::query()->whereKey($serviceIds->all())->get()->keyBy('id');

            if ($services->count() !== $serviceIds->count()) {
                throw (new ModelNotFoundException)->setModel(Services::class, $serviceIds->all());
            }

            $order = Order::create([
                'customer_name' => $validated['customer_name'],
                'whatsapp_number' => $validated['whatsapp_number'],
                'total_price' => 0,
                'status' => 'Antrean',
            ]);

            $totalPrice = 0;

            foreach ($items as $item) {
                $service = $services->get($item['service_id']);

                if ($service === null) {
                    throw (new ModelNotFoundException)->setModel(Services::class, [$item['service_id']]);
                }

                $subtotal = $service->price_per_unit * $item['qty_or_weight'];

                $order->orderDetails()->create([
                    'service_id' => $service->id,
                    'qty_or_weight' => $item['qty_or_weight'],
                    'subtotal' => $subtotal,
                ]);

                $totalPrice += $subtotal;
            }

            $order->update(['total_price' => $totalPrice]);
        });

        return redirect()->route('customer.index')->with('success', 'Pesanan berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function adminDashboard(): View
    {
        $orders = Order::with('orderDetails.service')->latest()->get();

        return view('dashboard', compact('orders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:Antrean,Dicuci,Selesai',
        ]);

        $order->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
