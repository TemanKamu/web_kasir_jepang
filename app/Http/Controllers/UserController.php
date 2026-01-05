<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
   public function index(Request $request)
    {
        $query = User::query();

        // Filter berdasarkan Nama atau Email
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter berdasarkan Tanggal Join (created_at)
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Filter berdasarkan Role
        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        // Ambil data terbaru
        $users = $query->latest()->get();

        return view('Dashboard.user', compact('users'));
    }


   public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:15',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id', // Sesuai tabel role di ERD
        ]);

        // Validasi: Cek jika role yang dipilih adalah Customer (ID 2)
        if ($request->role_id == 2) {
            $customerCount = User::where('role_id', 2)->count();
            if ($customerCount >= 1) {
                return response()->json([
                    'errors' => ['role_id' => ['Hanya diperbolehkan memiliki 1 akun Customer untuk tablet kasir.']]
                ], 422);
            }
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return response()->json(['message' => 'User berhasil dibuat']);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone_number' => 'required|string|max:20', // Tambahkan validasi ini
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number, // Tambahkan validasi ini
            'role_id' => $request->role_id,
        ]);

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return response()->json(['status' => 'success', 'message' => 'User berhasil diperbarui']);
    }

   public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['status' => 'success', 'message' => 'User berhasil dihapus']);
    }
}