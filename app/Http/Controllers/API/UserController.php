<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Traits\SiteTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    use SiteTrait;

    protected $storagePath = 'public/users/';

    public function __construct()
    {
        $this->middleware('can:create user')->only('store');
        $this->middleware('can:update user')->only(['show', 'update']);
        $this->middleware('can:delete user')->only('destroy');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $users = User::with('roles');

        return DataTables::of($users)
            ->addIndexColumn()
            ->editColumn('roles.name', fn ($data) => $data->roles->first()?->name)
            ->addColumn('editPermission', $user->can('update user'))
            ->addColumn('deletePermission', $user->can('delete user'))
            ->addColumn('action', fn ($data) => $data->id)
            ->rawColumns(['action'])
            ->filter(function ($query) use ($request) {
                if ($request->status) {
                    $query->where('status', $request->status);
                }
            }, true)
            ->make(true);
    }

    public function store(StoreUserRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $data['image'] = $this->storeFile($request->image, $this->storagePath);
            }

            $user = User::create($data);
            $user->assignRole($request->role);
            DB::commit();

            return $this->sendResponse(200, 'User Berhasil Dibuat!');
        } catch (\Exception $e) {
            DB::rollback();

            return $this->sendResponse(500, $e->getMessage());
        }
    }

    public function show(User $user)
    {
        return $this->sendResponse(200, 'Berhasil mendapatkan data user', $user->load('roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            if ($request->hasFile('image')) {
                Storage::delete("public/$user->image");
                $data['image'] = $this->storeFile($request->image, $this->storagePath);
            }

            if ($request->filled('newpassword')) {
                $data['password'] = $request->newpassword;
            }

            $user->update($data);

            if ($request->role != $user->getRoleNames()->first()) {
                $user->syncRoles($request->role);
            }

            DB::commit();

            return $this->sendResponse(200, 'Data User Berhasil Diubah');
        } catch (\Exception $e) {
            DB::rollback();

            return $this->sendResponse(500, $e->getMessage());
        }

        return response()->json($result);
    }

    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            if ($id == 'selected') {
                $users = User::whereIn('id', $request->input('users'));

                foreach ($users->get() as $user) {
                    if ($user->image) {
                        Storage::delete("public/$user->image");
                    }
                }

                $users->delete();
            } else {
                $user = User::findOrFail($id);
                if ($user->image) {
                    Storage::delete("public/$user->image");
                }

                $user->delete();
            }

            DB::commit();

            return $this->sendResponse(200, 'User Berhasil Dihapus!');
        } catch (\Exception $e) {
            DB::rollback();

            return $this->sendResponse(500, $e->getMessage());
        }

        return response()->json($result);
    }
}
