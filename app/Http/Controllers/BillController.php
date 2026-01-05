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
    public function index(Request $request)
    {
        $query = Bill::with(['user', 'orderedMenus.menu']);

        // Filter Search (ID atau Nomor Antrean)
        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%')
                ->orWhere('queue_number', 'like', '%' . $request->search . '%');
        }

        // Filter Tanggal (Baru)
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Urutkan Harga
        if ($request->filled('sort_price')) {
            $query->orderBy('amount_paid', $request->sort_price);
        } else {
            $query->orderBy('date', 'desc');
        }

        // Tetap gunakan pagination agar performa terjaga
        $bills = $query->paginate(10)->withQueryString();

        return view('bills.index', compact('bills'));
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
