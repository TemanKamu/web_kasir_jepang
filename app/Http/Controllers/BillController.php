<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
use App\Model\Menu;
use App\Model\OrderedMenu;
use App\Models\Bill;
use App\Models\ProofTransferPayment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BillController extends Controller
{
    /**
     * Dashboard Bill Index
     */
    public function index(Request $request)
    {
        $query = Bill::with(['user', 'orderedMenus.menu']);

        // Filter Search (UUID atau Nomor Antrean)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('queue_number', $request->search);
            });
        }

        // Filter Tanggal
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter Status (Pending / Completed)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bills = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('bills.index', compact('bills'));
    }

    public function processToPos($id) 
    {
        $bill = Bill::with('orderedMenus.menu')->findOrFail($id);
        
        // Simpan data bill ke dalam session
        session()->flash('process_bill', $bill);
        
        // Alihkan ke halaman POS (admin.blade.php)
        return redirect()->route('menus.index'); // Sesuaikan dengan nama route admin kamu
    }



   public function destroy($id)
    {
        $bill = Bill::findOrFail($id);
        
        // Kita ubah statusnya menjadi rejected, bukan hapus permanen
        $bill->update([
            'user_id' => Auth::id(),
            'status' => 'cancelled'
        ]);

        return redirect()->back()->with('success', 'Bill berhasil dibatalkan.');
    }
    

}
