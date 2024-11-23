<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Traits\SiteTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    use SiteTrait;

    protected $storagePath = 'public/users/';

    public function __construct()
    {
        $this->middleware('can:update profile');
    }

    public function update(UpdateProfileRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();

            $user = auth()->user();

            if ($request->hasFile('image')) {
                Storage::delete("public/$user->image");
                $data['image'] = $this->storeFile($request->image, $this->storagePath);
            }

            if ($request->filled('newpassword')) {
                $data['password'] = $request->newpassword;
            }

            $user->fill($data)->save();
            DB::commit();

            return $this->sendResponse(200, 'Profile Berhasil Diubah!');
        } catch (\Exception $e) {
            DB::rollback();

            return $this->sendResponse($e->getCode(), $e->getMessage());
        }
    }
}
