<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Settings\PermissionParentGroup;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class GroupUsersController extends Controller
{
    public function index()
    {
        $parentGroups = PermissionParentGroup::orderBy('order_number', 'asc')->get();
        return view('settings.group_users.index',[
            'parentGroups' => $parentGroups,
        ]);
    }

    public function show()
    {
        $roles = Role::with('permissions')->get();
        
        return response()->json($roles);
    }

    public function storePermission(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name'      => 'required|string|max:255|unique:permissions,name',
            'parent_id' => 'required|exists:permission_parent_group,id', // Memastikan ID grup ada di tabel
        ], [
            'name.unique'  => 'Nama permission ini sudah ada.',
            'parent_id.required' => 'Grup/Parent wajib dipilih.',
        ]);

        try {
            DB::transaction(function () use($request){
                // 2. Simpan Data
                // 'name' otomatis di-slug agar tidak ada spasi (contoh: 'tambah user' -> 'tambah-user')
                Permission::create([
                    'name'       => Str::slug($request->name),
                    'parent_id'  => $request->parent_id, // Menyimpan ID relasi ke tabel group
                    'guard_name' => 'web' 
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Permission berhasil ditambahkan.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method Edit
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        
        // Ambil data group yang memiliki permission, urutkan berdasarkan order_number
        $groups = PermissionParentGroup::with('permissions')->orderBy('order_number', 'asc')->get();
        
        // Ambil daftar ID permission yang sudah dimiliki oleh Role ini (untuk di-check / aktifkan otomatis)
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        $editing = true;

        return view('settings.group_users.create', compact('role', 'groups', 'rolePermissions', 'editing'));
    }

    // Method Create (Sebagai Referensi)
    public function create()
    {
        $groups = PermissionParentGroup::with('permissions')->orderBy('order_number', 'asc')->get();
        $rolePermissions = []; // Kosongkan karena role baru belum punya permission

        $editing = false;

        return view('settings.group_users.create', compact('groups', 'rolePermissions', 'editing'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
        ]);

        DB::transaction(function () use($request){
            $role = Role::create([
                'name'       => $request->name,
                'guard_name' => 'web',
            ]);
    
            // assign permissions
            if ($request->permissions) {
    
                $role->syncPermissions($request->permissions);
            }

            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        });


        return to_route('settings.group_user.index')->with('success', 'Role berhasil dibuat.');
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
        ]);

        DB::transaction(function () use($role, $request){
            $role->update([
                'name' => $request->name,
            ]);
    
            // update permissions role
            $role->syncPermissions(
                $request->permissions ?? []
            );

            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        });


        return to_route('settings.group_user.index')->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        try {

            DB::beginTransaction();

            // hapus relasi permission
            DB::table('role_has_permissions')
                ->where('role_id', $role->id)
                ->delete();

            // hapus relasi user-role
            DB::table('model_has_roles')
                ->where('role_id', $role->id)
                ->delete();

            // hapus role
            $role->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil dihapus.',
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
