<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Mendapatkan semua data user
    public function index()
    {
        return User::orderBy('created_at', 'desc')->get();
    }

    // Menyimpan data user baru
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|integer'
        ]);

        $data['password'] = Hash::make($data['password']); // Hash password

        $user = User::create($data);

        return response()->json($user, 201);
    }

    // Mendapatkan detail user berdasarkan ID
    public function show($id)
    {
        $user = User::find($id);

        if ($user) {
            return response()->json($user);
        } else {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }
    }

    // Mengupdate data user
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $data = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:6',
            'role' => 'sometimes|integer'
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']); // Hash password jika diupdate
        }

        $user->update($data);

        return response()->json($user);
    }

    // Menghapus user berdasarkan ID
    public function destroy($id)
    {
        $user = User::find($id);

        if ($user) {
            $user->delete();
            return response()->json(['message' => 'User berhasil dihapus']);
        } else {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }
    }
}
