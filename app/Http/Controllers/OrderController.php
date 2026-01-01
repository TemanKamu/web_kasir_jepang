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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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

    public function confirmCart(Request $request, $id)
    {
        try {
            $proofPath = null;

            // 1. Cek File Bukti Bayar
            if ($request->payment_method !== 'cash') {
                if (!$request->hasFile('payment_proof')) {
                    return response()->json(['status' => 'error', 'message' => 'Bukti transfer/QRIS wajib diunggah!'], 422);
                }
                $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
            }

            $bill = DB::transaction(function () use ($request, $id, $proofPath) {
                $cartGroup = \App\Models\CartGroup::with('items')->findOrFail($id);

                // 2. Buat Bill Final
                $newBill = \App\Models\Bill::create([
                    'code' => (string) \Illuminate\Support\Str::uuid(),
                    'queue_number' => $cartGroup->queue_number,
                    'user_id' => Auth::id() ?? 1,
                    'service_type' => $cartGroup->service_type,
                    'payment_method' => $request->payment_method,
                    'amount_paid' => $request->amount_paid,
                    'change' => $request->change,
                    'total_price' => $request->total_price,
                    'status' => 'completed',
                    'date' => now(),
                ]);

                // 3. LOGIKA BARU: Input ke tabel proof_transfer_payments
                if ($proofPath) {
                    \App\Models\ProofTransferPayment::create([
                        'bill_id' => $newBill->id,
                        'image' => $proofPath, // sesuaikan nama kolom di tabel kamu (misal: proof_image atau image)
                        // tambahkan field lain jika ada, misal: 'amount' => $request->amount_paid
                    ]);
                }

                // 4. Pindahkan item ke OrderedMenu
                $items = is_string($request->items) ? json_decode($request->items, true) : $request->items;
                foreach ($items as $item) {
                    \App\Models\OrderedMenu::create([
                        'bill_id' => $newBill->id,
                        'menu_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'total_price' => $item['price'] * $item['quantity'],
                    ]);
                }

                // 5. Update status CartGroup
                $cartGroup->update(['status' => 'confirmed']);

                return $newBill;
            });

            return response()->json([
                'status' => 'success', 
                'message' => 'Pembayaran Berhasil!',
                'bill_id' => $bill->id,
                'payment_proof_url' => $proofPath ? asset('storage/' . $proofPath) : null
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ], 500);
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
