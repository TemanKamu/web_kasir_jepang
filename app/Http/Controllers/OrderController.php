<?php

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Bill;
use App\Models\OrderedMenu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // OrderController.php
    public function store(Request $request) 
    {
        try {
            $cartGroup = DB::transaction(function () use ($request) {
                // Generate Nomor Antrian Otomatis untuk hari ini
                $todayQueue = \App\Models\CartGroup::whereDate('created_at', now())->count() + 1;

                $newGroup = \App\Models\CartGroup::create([
                    'queue_number' => $todayQueue, // Nomor antrian di-set di sini
                    'service_type' => $request->service_type,
                    'total_price' => $request->total_price,
                    'status' => 'pending',
                ]);

                foreach ($request->items as $item) {
                    \App\Models\Cart::create([
                        'cart_group_id' => $newGroup->id,
                        'menu_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                    ]);
                }
                return $newGroup;
            });
            // Log::info('Triggering Broadcast untuk Cart ID: ' . $cartGroup->id);
            broadcast(new OrderPlaced($cartGroup->load('items.menu')))->toOthers();
            return response()->json([
                'status' => 'success', 
                'message' => 'Pesanan dikirim!',
                'queue_number' => $cartGroup->queue_number // Beritahu customer nomor mereka
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function confirmCart(Request $request, $id = null)
    {
        try {
            $proofPath = null;
            if ($request->payment_method !== 'cash') {
                if (!$request->hasFile('payment_proof')) {
                    return response()->json(['status' => 'error', 'message' => 'Bukti bayar wajib ada!'], 422);
                }
                $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
            }

            // Simpan hasil transaction ke variabel $bill
            $bill = DB::transaction(function () use ($request, $id, $proofPath) {
                if ($id && $id !== 'null') {
                    $cartGroup = \App\Models\CartGroup::findOrFail($id);
                    $queueNumber = $cartGroup->queue_number;
                    $serviceType = $cartGroup->service_type;
                    $cartGroup->update(['status' => 'confirmed']);
                } 
                else {
                    // Perbaikan: Ambil nomor antrian terbaru dari Bill atau CartGroup hari ini
                    $countCart = \App\Models\CartGroup::whereDate('created_at', now())->count();
                    $countBill = \App\Models\Bill::whereDate('created_at', now())->count();
                    $queueNumber = max($countCart, $countBill) + 1;
                    $serviceType = $request->service_type ?? 'dine_in';
                }

                $newBill = \App\Models\Bill::create([
                    'code' => (string) \Illuminate\Support\Str::uuid(),
                    'queue_number' => $queueNumber,
                    'user_id' => Auth::id() ?? 1,
                    'service_type' => $serviceType,
                    'payment_method' => $request->payment_method,
                    'amount_paid' => (int) $request->amount_paid,
                    'change' => (int) ($request->change ?? 0), 
                    'total_price' => (int) $request->total_price,
                    'status' => 'completed',
                    'date' => now(),
                ]);

                $items = is_string($request->items) ? json_decode($request->items, true) : $request->items;
                foreach ($items as $item) {
                    \App\Models\OrderedMenu::create([
                        'bill_id' => $newBill->id,
                        'menu_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'total_price' => $item['price'] * $item['quantity'],
                    ]);
                }

                return $newBill;
            });

            return response()->json([
                'status' => 'success', 
                'bill_id' => $bill->id,
                'queue_number' => $bill->queue_number, // <--- INI WAJIB ADA
                'payment_proof_url' => $proofPath ? asset('storage/' . $proofPath) : null
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
