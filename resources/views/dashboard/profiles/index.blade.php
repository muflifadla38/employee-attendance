@extends('layouts.dashboard')
@push('token')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <!--begin::Profile Card-->
    <div class="mb-5 card mb-xl-10">
        <div class="border-0 cursor-pointer card-header" role="button" data-bs-toggle="collapse"
            data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
            <div class="m-0 card-title">
                <h3 class="m-0 fw-bold">{{ $title }}</h3>
            </div>
        </div>
        <div id="kt_account_settings_profile_details" class="collapse show">
            <form id="kt_account_profile_details_form" class="form" method="PUT" action="{{ route('api.profile.update') }}"
                enctype="multipart/form-data">
                <div class="card-body border-top p-9">
                    <div class="mb-6 row">
                        <label class="col-lg-3 col-form-label fw-semibold fs-6">Profile Image</label>
                        <div class="col-lg-9">
                            <div @class([
                                'image-input image-input-outline',
                                'image-input-empty' => !auth()->user()->image,
                            ]) data-kt-image-input="true"
                                style="background-image: url('assets/media/svg/avatars/blank.svg')">
                                @if (auth()->user()->image)
                                    <div class="image-input-wrapper w-125px h-125px"
                                        style="background-image: url('storage/{{ auth()->user()->image }}')"></div>
                                @else
                                    <div class="image-input-wrapper w-125px h-125px" style="background-image: none;"></div>
                                @endif
                                <label class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                    data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                    <i class="ki-duotone ki-pencil fs-7">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="file" name="image" id="image" accept=".png, .jpg, .jpeg" />
                                    <input type="hidden" name="avatar_remove" />
                                </label>
                                <span class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                    data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                                    <i class="ki-duotone ki-cross fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                    data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove avatar">
                                    <i class="ki-duotone ki-cross fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div class="form-text">Allowed file types: png, jpg, jpeg (max: 2MB)</div>
                        </div>
                    </div>
                    <div class="mb-6 row">
                        <label class="col-lg-3 col-form-label required fw-semibold fs-6">Name</label>
                        <div class="col-lg-9 fv-row">
                            <input type="text" name="name" id="name"
                                class="mb-3 form-control form-control-lg form-control-solid mb-lg-0"
                                placeholder="First name" value="{{ auth()->user()->name }}" />
                        </div>
                    </div>
                    <div class="mb-6 row">
                        <label class="col-lg-3 col-form-label required fw-semibold fs-6">Role</label>
                        <div class="col-lg-9 fv-row">
                            <input type="text" class="form-control form-control-lg form-control-solid"
                                placeholder="{{ auth()->user()->getRoleNames()->first() }}" disabled />
                        </div>
                    </div>
                    <div class="mb-6 row">
                        <label class="col-lg-3 col-form-label fw-semibold fs-6">
                            <span class="required">Email</span>
                        </label>
                        <div class="col-lg-9 fv-row">
                            <input type="email" class="form-control form-control-lg form-control-solid"
                                placeholder="{{ auth()->user()->email }}" disabled />
                        </div>
                    </div>

                    <div class="mb-6 row" data-kt-password-meter="true">
                        <label class="col-lg-3 col-form-label fw-semibold fs-6">Password</label>
                        <div class="col-lg-9">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-0 fv-row">
                                        <input type="password" class="form-control form-control-lg form-control-solid"
                                            name="currentpassword" id="currentpassword" placeholder="Current Password" />
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-0 fv-row">
                                        <input type="password" class="form-control form-control-lg form-control-solid"
                                            name="newpassword" id="newpassword" placeholder="New Password" />
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-0 fv-row">
                                        <input type="password" class="form-control form-control-lg form-control-solid"
                                            name="newpassword_confirmation" id="newpassword_confirmation"
                                            placeholder="Confirm New Password" />
                                    </div>
                                </div>
                            </div>
                            <div class="mb-5 form-text">Password must be at least 8 character and contain symbols
                            </div>
                        </div>
                    </div>
                </div>
                <div class="py-6 card-footer d-flex justify-content-end px-9">
                    <button type="reset"
                        class="discard-button btn btn-light btn-active-light-primary me-2 bg-">Discard</button>
                    <button type="submit" class="btn btn-primary" id="kt_account_profile_details_submit">
                        <i class="ki-duotone ki-check fs-3 d-none"></i>
                        <span class="indicator-label">Save Changes</span>
                        <span class="indicator-progress">Please wait...
                            <span class="align-middle spinner-border spinner-border-sm ms-2"></span></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!--end::Profile Card-->

    @push('scripts')
        <script src="{{ asset('assets/js/custom/account/profile.js') }}"></script>
    @endpush
@endsection
