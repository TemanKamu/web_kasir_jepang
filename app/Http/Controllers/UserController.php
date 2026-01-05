<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        // Gunakan paginate agar web tidak berat jika user sudah ribuan
        $users = $query->latest()->paginate(10)->withQueryString();

        return view('Dashboard.user', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone_number' => 'required|string|max:15',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Proteksi: Cek duplikasi akun Customer
        if ($request->role_id == 2) {
            if (User::where('role_id', 2)->exists()) {
                return response()->json([
                    'errors' => ['role_id' => ['Hanya boleh ada 1 akun Customer untuk sistem tablet.']]
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
            'phone_number' => 'required|string|max:15', // Disamakan dengan store
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8', // Tambahkan validasi password jika diisi
        ]);

        // Proteksi Update: Jangan sampai merubah admin jadi customer jika sudah ada customer
        if ($request->role_id == 2 && $user->role_id != 2) {
            if (User::where('role_id', 2)->exists()) {
                return response()->json([
                    'errors' => ['role_id' => ['Gagal! Akun Customer sudah tersedia.']]
                ], 422);
            }
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'role_id' => $request->role_id,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return response()->json(['status' => 'success', 'message' => 'User diperbarui']);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Opsional: Jangan biarkan admin menghapus dirinya sendiri
        if ($user->id === Auth::id()) {
            return response()->json(['status' => 'error', 'message' => 'Tidak bisa menghapus akun sendiri!'], 403);
        }

        $user->delete();
        return response()->json(['status' => 'success', 'message' => 'User dihapus']);
    }
}