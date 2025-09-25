<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    /** LIST USERS */
    public function index()
    {
        $users = User::orderBy('_id', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /** FORM CREATE */
    public function create()
    {
        $roles = Role::orderBy('_id')->get(['_id','name']);
        return view('admin.users.create', compact('roles'));
    }

    /** SIMPAN CREATE */
    public function store(Request $request)
    {
                
        $allowedRoleIds = Role::all()->map(fn($r) => (int)$r->_id)->toArray();
        $data = $request->validate([
            'name'     => ['required','string','max:100'],
            'email'    => ['required','email','max:150', Rule::unique('users','email')],
            'role_id'  => ['required', Rule::in($allowedRoleIds)],
            'password' => ['nullable','string','min:8'], // sementara hapus 'confirmed' kalau tidak pakai password_confirmation
        ]);

        // Cast role_id ke integer
        $data['role_id'] = (int) $data['role_id'];

        // Si
        $plain = $data['password'] ?? Str::password(12);

        User::create([
            'name'     => $data['name'],
            'email'    => strtolower($data['email']),
            'role_id'  => $data['role_id'],
            'password' => Hash::make($plain),
        ]);


        return redirect()->route('admin.users.index')->with('status','Anggota berhasil ditambahkan.');
    }

    /** FORM EDIT */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /** UPDATE */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $allowedRoleIds = Role::all()->map(fn($r) => (int)$r->_id)->toArray();

        $data = $request->validate([
            'name'     => ['required','string','max:100'],
            'email'    => ['required','email','max:150', Rule::unique('users','email')->ignore($id,'_id')],
            'role_id'  => ['required', Rule::in($allowedRoleIds)],
            'password' => ['nullable','string','min:8'],
        ]);

        $data['role_id'] = (int)$data['role_id'];

        if(!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('status','Perubahan disimpan.');
    }

    /** DELETE */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        if((string) auth()->user()->_id === (string) $user->_id) {
            return back()->withErrors(['delete' => 'Tidak bisa hapus akun sendiri.']);
        }

        $user->delete();

        return back()->with('status','Anggota dihapus.');
    }
}
