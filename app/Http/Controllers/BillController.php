<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
use App\Model\Menu;
use App\Model\OrderedMenu;
use App\Models\Bill;
use App\Models\ProofTransferPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BillController extends Controller
{
      public function index()
    {
        $bills = Bill::with(['user', 'orderedMenus.menu'])->latest('date')->get();
        return view('bills.index', compact('bills'));
    }

    public function create()
    {
        $menus = Menu::where('status', 'available')->with('category')->get();
        $users = User::all();
        return view('bills.create', compact('menus', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'customer_name' => 'nullable|string|max:255',
            'amount_paid' => 'required|integer|min:0',
            'payment_method' => 'required|in:cash,transfer,qris',
            'service_type' => 'required|in:dine_in,take_away',
            'menus' => 'required|array|min:1',
            'menus.*.menu_id' => 'required|exists:menus,id',
            'menus.*.quantity' => 'required|integer|min:1',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        DB::beginTransaction();
        try {
            // Calculate total
            $total = 0;
            $orderItems = [];
            foreach ($request->menus as $item) {
                $menu = Menu::find($item['menu_id']);
                $subtotal = $menu->price * $item['quantity'];
                $total += $subtotal;
                
                $orderItems[] = [
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'quantity' => $item['quantity'],
                    'price' => $menu->price,
                    'subtotal' => $subtotal
                ];
            }

            // Create bill
            $bill = Bill::create([
                'code' => Str::uuid(),
                'queue_number' => Bill::whereDate('date', now())->max('queue_number') + 1,
                'user_id' => $request->user_id ?? null,
                'amount_paid' => $request->amount_paid,
                'change' => $request->amount_paid - $total,
                'payment_method' => $request->payment_method,
                'service_type' => $request->service_type,
                'status' => 'pending',
                'date' => now()
            ]);

            // Create ordered menus
            foreach ($request->menus as $item) {
                $menu = Menu::find($item['menu_id']);
                OrderedMenu::create([
                    'bill_id' => $bill->id,
                    'menu_id' => $item['menu_id'],
                    'quantity' => $item['quantity'],
                    'total_price' => $menu->price * $item['quantity']
                ]);
                
                // Update count sold
                $menu->increment('count_sold', $item['quantity']);
            }

            // Upload proof if transfer/qris
            if (in_array($request->payment_method, ['transfer', 'qris']) && $request->hasFile('proof_image')) {
                $path = $request->file('proof_image')->store('proofs', 'public');
                ProofTransferPayment::create([
                    'bill_id' => $bill->id,
                    'image' => $path,
                    'image_url' => Storage::url($path)
                ]);
            }

            DB::commit();
            
            // Load relationships for broadcasting
            $bill->load(['user', 'orderedMenus.menu', 'proofTransferPayment']);
            
            // Broadcast new order to cashier tablet
            broadcast(new OrderCreated($bill))->toOthers();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order created successfully',
                    'data' => [
                        'bill_id' => $bill->id,
                        'code' => $bill->code,
                        'queue_number' => $bill->queue_number,
                        'total' => $total,
                        'change' => $bill->change
                    ]
                ], 201);
            }
            
            return redirect()->route('bills.show', $bill)->with('success', 'Bill created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create order: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to create bill: ' . $e->getMessage());
        }
    }

    public function show(Bill $bill)
    {
        $bill->load(['user', 'orderedMenus.menu', 'proofTransferPayment']);
        return view('bills.show', compact('bill'));
    }

    public function updateStatus(Request $request, Bill $bill)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled'
        ]);

        $oldStatus = $bill->status;
        $bill->update(['status' => $request->status]);
        
        // Load relationships for broadcasting
        $bill->load(['user', 'orderedMenus.menu', 'proofTransferPayment']);
        
        // Broadcast status change to customer tablet
        broadcast(new OrderStatusUpdated($bill, $oldStatus, $request->status))->toOthers();
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'data' => [
                    'bill_id' => $bill->id,
                    'status' => $bill->status
                ]
            ]);
        }
        
        return redirect()->back()->with('success', 'Bill status updated successfully');
    }

    public function destroy(Bill $bill)
    {
        DB::beginTransaction();
        try {
            if ($bill->proofTransferPayment && $bill->proofTransferPayment->image) {
                Storage::disk('public')->delete($bill->proofTransferPayment->image);
            }

            foreach ($bill->orderedMenus as $orderedMenu) {
                $orderedMenu->menu->decrement('count_sold', $orderedMenu->quantity);
            }

            $billId = $bill->id;
            $bill->delete();
            
            // Broadcast order deleted
            broadcast(new OrderStatusUpdated(['id' => $billId], null, 'deleted'))->toOthers();
            
            DB::commit();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order deleted successfully'
                ]);
            }
            
            return redirect()->route('bills.index')->with('success', 'Bill deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete order: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Failed to delete bill: ' . $e->getMessage());
        }
    }
    
    // API untuk Customer Tablet
    public function getAvailableMenus()
    {
        $menus = Menu::where('status', 'available')
            ->with('category')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $menus
        ]);
    }
    
    // API untuk Cashier Tablet
    public function getPendingOrders()
    {
        $orders = Bill::where('status', 'pending')
            ->with(['user', 'orderedMenus.menu', 'proofTransferPayment'])
            ->latest('date')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
    
    public function getTodayOrders()
    {
        $orders = Bill::whereDate('date', now())
            ->with(['user', 'orderedMenus.menu', 'proofTransferPayment'])
            ->latest('date')
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}
