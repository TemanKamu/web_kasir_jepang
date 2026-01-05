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
     * Digunakan oleh Customer (Tablet) untuk memesan.
     * Pesanan masuk ke tabel Bill dengan status 'pending'.
     */
   // Bagian store() yang diperbaiki
    public function store(Request $request) 
    {
        try {
            $bill = DB::transaction(function () use ($request) {
                // Gunakan tabel Bill untuk hitung antrian agar sinkron
                $todayQueue = Bill::whereDate('created_at', now())->count() + 1;

                $newBill = Bill::create([
                    'code' => (string) Str::uuid(),
                    'queue_number' => $todayQueue,
                    'user_id' => Auth::id() ?? 1,
                    'service_type' => $request->service_type,
                    'total_price' => $request->total_price,
                    'status' => 'pending',
                    'payment_method' => 'cash', 
                    'amount_paid' => 0,
                    'change' => 0,
                    'date' => now(),
                ]);

                // Loop dari request (ini benar menggunakan $request->items karena dari frontend)
                foreach ($request->items as $item) {
                    OrderedMenu::create([
                        'bill_id' => $newBill->id,
                        'menu_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'total_price' => $item['price'] * $item['quantity'],
                    ]);
                }

                return $newBill;
            });

            // Broadcast dengan relasi yang benar
            broadcast(new OrderPlaced($bill->load('orderedMenus.menu')))->toOthers();

            return response()->json([
                'status' => 'success', 
                'message' => 'Pesanan dikirim!',
                'queue_number' => $bill->queue_number
            ]);
        } catch (\Exception $e) {
            // Log error agar Anda bisa lihat di storage/logs/laravel.log jika gagal
            Log::error("Order Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Digunakan oleh Kasir untuk mengonfirmasi pembayaran (Status pending -> completed)
     * ATAU untuk pesanan langsung dari Kasir (ID null).
     */
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

            $bill = DB::transaction(function () use ($request, $id, $proofPath) {
                
                // JIKA PESANAN SUDAH ADA (DARI CUSTOMER / STATUS PENDING)
                if ($id && $id !== 'null') {
                    $existingBill = Bill::with('orderedMenus')->findOrFail($id);
                    
                    $existingBill->update([
                        'user_id' => Auth::id() ?? $existingBill->user_id,
                        'payment_method' => $request->payment_method,
                        'amount_paid' => (int) $request->amount_paid,
                        'change' => (int) ($request->change ?? 0),
                        'status' => 'completed',
                    ]);

                    // Update count_sold untuk setiap menu yang sudah dipesan customer sebelumnya
                    foreach ($existingBill->orderedMenus as $orderItem) {
                        \App\Models\Menu::where('id', $orderItem->menu_id)
                            ->increment('count_sold', $orderItem->quantity);
                    }

                    if ($proofPath) {
                        \App\Models\ProofTransferPayment::create([
                            'bill_id' => $existingBill->id,
                            'image' => $proofPath,
                        ]);
                    }
                    event(new \App\Events\DataUpdated('menu_updated'));
                    return $existingBill;
                } 
                
                // JIKA PESANAN BARU (ADMIN LANGSUNG INPUT DI KASIR)
                else {
                    $todayQueue = Bill::whereDate('created_at', now())->count() + 1;

                    $newBill = Bill::create([
                        'code' => (string) Str::uuid(),
                        'queue_number' => $todayQueue,
                        'user_id' => Auth::id() ?? 1,
                        'service_type' => $request->service_type ?? 'dine_in',
                        'payment_method' => $request->payment_method,
                        'amount_paid' => (int) $request->amount_paid,
                        'change' => (int) ($request->change ?? 0), 
                        'total_price' => (int) $request->total_price,
                        'status' => 'completed',
                        'date' => now(),
                    ]);

                    $items = is_string($request->items) ? json_decode($request->items, true) : $request->items;
                    foreach ($items as $item) {
                        OrderedMenu::create([
                            'bill_id' => $newBill->id,
                            'menu_id' => $item['id'],
                            'quantity' => $item['quantity'],
                            'total_price' => $item['price'] * $item['quantity'],
                        ]);

                        // Tambahkan count_sold untuk pesanan baru admin
                        \App\Models\Menu::where('id', $item['id'])
                            ->increment('count_sold', $item['quantity']);
                    }

                    if ($proofPath) {
                        \App\Models\ProofTransferPayment::create([
                            'bill_id' => $newBill->id,
                            'image' => $proofPath,
                        ]);
                    }

                    event(new \App\Events\DataUpdated('menu_updated'));
                    return $newBill;
                }
            });

            return response()->json([
                'status' => 'success', 
                'bill_id' => $bill->id,
                'queue_number' => $bill->queue_number,
                'payment_proof_url' => $proofPath ? asset('storage/' . $proofPath) : null
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}