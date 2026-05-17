<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $roles = DB::table('roles')->get();
        return view('user.index', [
            'roles' => $roles,
        ]);
    }

    public function show()
    {
        $users = User::with('roles')
            ->latest()
            ->get()
            ->map(function ($user) {

                return [

                    'id' => $user->id,

                    'name' => $user->name,

                    'email' => $user->email,

                    'phone' => $user->phone,

                    'role' => $user->roles->first()?->name,

                    'status' => $user->status
                        ? 'active'
                        : 'inactive',

                    'uuid' => $user->uuid,

                    'created_at' => $user->created_at,

                ];

            });

        return response()->json($users);
    }

    public function create()
    {
        $roles = DB::table('roles')->get();
        return view('user.create', [
            'roles' => $roles,
            'editing' => false,
        ]);
    }

    public function edit(User $user)
    {
        $roles = DB::table('roles')->get();
        return view('user.create', [
            'roles' => $roles,
            'user' => $user,
            'editing' => true,
        ]);
    }

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'name' => 'required|string|max:255',

            'username' => 'required|string|max:100|unique:users,username',

            'email' => 'required|email|unique:users,email',

            'phone' => 'nullable|string|max:30',

            'password' => 'required|min:6|confirmed',

            'role_id' => 'required|exists:roles,id',    

            'profile_picture' => '
                nullable|
                image|
                mimes:jpg,jpeg,png,webp|
                max:5120
            ',

        ]);

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Upload Profile Picture
            |--------------------------------------------------------------------------
            */

            $profilePicture = null;

            if ($request->hasFile('profile_picture')) {

                $file = $request->file('profile_picture');

                $profilePicture = time() . '_' .
                    $file->getClientOriginalName();

                $file->storeAs(
                    'profile-picture',
                    $profilePicture,
                    'public'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Create User
            |--------------------------------------------------------------------------
            */

            $user = User::create([

                'name' => $request->name,

                'username' => $request->username,

                'email' => $request->email,

                'phone' => $request->phone,

                'password' => Hash::make($request->password),

                'avatar' => $profilePicture,

                'status' => $request->status ? 1 : 0,

                'created_by' => auth()->id(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Assign Role
            |--------------------------------------------------------------------------
            */

            $role = Role::findById($request->role_id);

            $user->assignRole($role);

            DB::commit();

            /*
            |--------------------------------------------------------------------------
            | Success Alert
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('master.user.index')
                ->with('success', 'User created successfully');
        } catch (Exception $e) {

            DB::rollBack();

            dd($e->getMessage());

            if (!empty($profilePicture)) {

                Storage::disk('public')
                    ->delete('profile-picture/' . $profilePicture);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'name' => '
                required|
                string|
                max:255
            ',

            'username' => '
                required|
                string|
                max:100|
                unique:users,username,' . $user->id,
            
            'email' => '
                required|
                email|
                unique:users,email,' . $user->id,

            'phone' => '
                nullable|
                string|
                max:30
            ',

            'role_id' => '
                required
            ',

            'profile_picture' => '
                nullable|
                image|
                mimes:jpg,jpeg,png,webp|
                max:5120
            ',

        ]);

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Upload Profile Picture
            |--------------------------------------------------------------------------
            */

            $profilePicture = $user->avatar;

            if ($request->hasFile('profile_picture')) {

                /*
                |--------------------------------------------------------------------------
                | Delete Old Image
                |--------------------------------------------------------------------------
                */

                if (
                    !empty($user->avatar) &&
                    Storage::disk('public')->exists(
                        'profile-picture/' . $user->avatar
                    )
                ) {

                    Storage::disk('public')->delete(
                        'profile-picture/' . $user->avatar
                    );

                }

                /*
                |--------------------------------------------------------------------------
                | Upload New Image
                |--------------------------------------------------------------------------
                */

                $file = $request->file('profile_picture');

                $profilePicture =
                    time() . '_' .
                    $file->getClientOriginalName();

                $file->storeAs(
                    'profile-picture',
                    $profilePicture,
                    'public'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | Update User
            |--------------------------------------------------------------------------
            */

            $user->update([

                'name' => $request->name,

                'username' => $request->username,

                'email' => $request->email,

                'phone' => $request->phone,

                'avatar' => $profilePicture,

                'status' => $request->status ? 1 : 0,

                'updated_by' => auth()->id(),

            ]);

            /*
            |--------------------------------------------------------------------------
            | Sync Role
            |--------------------------------------------------------------------------
            */

            $role = \Spatie\Permission\Models\Role::findById(
                $request->role_id
            );

            $user->syncRoles([$role]);

            DB::commit();

            /*
            |--------------------------------------------------------------------------
            | Success
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('master.user.index')
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());

        }
    }

    public function destroy(User $user)
    {
        DB::beginTransaction();

        try {
            /*
            |--------------------------------------------------------------------------
            | Soft Delete
            |--------------------------------------------------------------------------
            */
            $user->update([
                'deleted_by' => auth()->id(),
            ]);

            $user->delete();

            DB::commit();

            /*
            |--------------------------------------------------------------------------
            | Success Response
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' => true,

                'message' => 'User deleted successfully.'

            ]);

        } catch (Exception $e) {

            DB::rollBack();

            /*
            |--------------------------------------------------------------------------
            | Error Response
            |--------------------------------------------------------------------------
            */

            return response()->json([

                'success' => false,

                'message' => $e->getMessage(),

            ], 500);

        }
    }
}
