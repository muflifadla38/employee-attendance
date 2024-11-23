<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use App\Traits\SiteTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    use SiteTrait;

    protected $storagePath = 'public/users/';

    protected $userData = [
        'name',
        'email',
        'image',
        'username',
        'password',
        'status',
    ];

    public function __construct()
    {
        $this->middleware('can:create employee')->only(['store']);
        $this->middleware('can:update employee')->only(['show', 'update']);
        $this->middleware('can:delete employee')->only('destroy');

        $this->userData = array_flip($this->userData);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $employees = Employee::with('user:id,name,image,email,status');

        return DataTables::of($employees)
            ->editColumn('user.name', fn ($data) => $data->user?->name)
            ->addColumn('editPermission', $user->can('update employee'))
            ->addColumn('deletePermission', $user->can('delete employee'))
            ->addColumn('action', fn ($data) => $data->id)
            ->rawColumns(['action'])
            ->filter(function ($query) use ($request) {
                $query->whereHas('user', function ($query) use ($request) {
                    $query->where('status', $request->status);
                });
            })
            ->make(true);
    }

    public function store(StoreEmployeeRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['image'] = $this->storeFile($request->image, $this->storagePath);

            $userData = array_intersect_key($data, $this->userData);
            $user = User::make($userData);
            $user->save();
            $user->assignRole('employee');

            $data = array_merge($data, ['user_id' => $user->id]);
            $employeeData = array_diff_key($data, $this->userData);
            Employee::make($employeeData)->save();

            DB::commit();

            return $this->sendResponse(200, 'Pegawai Berhasil Dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendResponse(500, $e->getMessage());
        }

        return response()->json($result);
    }

    public function show(Employee $employee)
    {
        $employee = $employee->load('user');
        $employee->user->load('roles');

        return $this->sendResponse(200, 'Berhasil mendapatkan data pegawai', $employee);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $employeeData = array_diff_key($data, $this->userData);
            $userData = array_intersect_key($data, $this->userData);

            if (count($employeeData)) {
                $employee = $employee->fill($employeeData);
                $employee->save();
            }

            if (count($userData)) {
                $user = $employee->user;

                if ($request->hasFile('image')) {
                    Storage::delete($this->storagePath.$user->image);
                    $userData['image'] = $this->storeFile($request->image, $this->storagePath);
                }

                $user = $user->fill($userData);
                $user->save();
            }

            DB::commit();

            return $this->sendResponse(200, 'Berhasil mengubah data pegawai!');
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendResponse(500, $e->getMessage());
        }

        return response()->json($result);
    }
}
