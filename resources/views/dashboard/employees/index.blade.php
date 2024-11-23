@extends('layouts.dashboard')
@push('token')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('styles')
    <link href="{{ asset('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
@endpush


@section('content')
    <x-molecules.card>
        <x-slot:header class="pt-6 border-0">
            <x-slot:title>
                <div class="my-1 d-flex align-items-center position-relative">
                    <x-atoms.icon class="position-absolute ms-5" icon="magnifier" path="2" size="3" />
                    <x-atoms.input data-kt-employee-table-filter="search" class="w-250px ps-13"
                        placeholder="Cari pegawai" />
                </div>
            </x-slot:title>
            <x-slot:toolbar>
                <div class="d-flex justify-content-end" data-kt-employee-table-toolbar="base">
                    <div>
                        <x-atoms.button class="me-3" color="light-primary" data-kt-menu-trigger="click"
                            data-kt-menu-placement="bottom-end">
                            <x-atoms.icon icon="filter" path="2" />
                            Filter
                        </x-atoms.button>
                        <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                            <div class="py-5 px-7">
                                <div class="fs-5 text-dark fw-bold">Filter Options</div>
                            </div>
                            <div class="border-gray-200 separator"></div>
                            <div class="py-5 px-7" data-kt-employee-table-filter="form">
                                <div class="mb-10">
                                    <div class="mb-5">
                                        <x-atoms.label class="fs-6 fw-semibold" value="Status:" :required="false" />
                                        <x-atoms.checkbox class="form-switch form-switch-sm custom pe-auto"
                                            :checked="true">
                                            <x-slot:input role="button" name="status-filter" value="active"></x-slot:input>
                                            <x-atoms.label class="form-check-label" value="Active" :required="false" />
                                        </x-atoms.checkbox>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="reset"
                                            class="px-6 btn btn-light btn-active-light-primary fw-semibold me-2"
                                            data-kt-menu-dismiss="true" data-kt-employee-table-filter="reset">Reset</button>
                                        <button type="submit" class="px-6 btn btn-primary fw-semibold"
                                            data-kt-menu-dismiss="true"
                                            data-kt-employee-table-filter="filter">Apply</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @can('create employee')
                        <x-atoms.button class="me-3" id="button_add_employee" color="primary" data-bs-toggle="modal"
                            data-bs-target="#kt_modal_add_employee">
                            <x-atoms.icon icon="plus" />
                            Add Employee
                        </x-atoms.button>
                    @endcan
                </div>
                <div class="d-flex justify-content-end align-items-center d-none" data-kt-employee-table-toolbar="selected">
                    <div class="fw-bold me-5">
                        <span class="me-2" data-kt-employee-table-select="selected_count"></span>Selected
                    </div>
                    <button type="button" class="btn btn-danger" data-kt-employee-table-select="delete_selected">Activate/
                        Inactive Employee</button>
                </div>
            </x-slot:toolbar>
        </x-slot:header>
        <x-slot:body class="py-4">
            <input type="hidden" id="table-url" value="{{ route('api.employees.index') }}">
            <x-molecules.table id="kt_table_employees" class="fs-6 gy-5">
                <x-slot:head>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-200px">Employee</th>
                        <th class="min-w-125px">Email</th>
                        <th class="min-w-125px">Date of Birth</th>
                        <th class="min-w-100px">City</th>
                        <th class="min-w-100px">Status</th>
                        <th class="text-end min-w-100px">Actions</th>
                    </tr>
                </x-slot:head>
                <x-slot:body></x-slot:body>
            </x-molecules.table>
        </x-slot:body>
    </x-molecules.card>

    <!--begin::Modal Add Employee-->
    <div class="modal fade" id="kt_modal_add_employee" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog mw-750px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Tambah Pegawai</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <x-atoms.icon icon="cross" path="2" size="1" />
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <div class="stepper stepper-links d-flex flex-column" id="kt_add_employee_stepper">
                        <div class="stepper-nav">
                            <div class="stepper-item current" data-kt-stepper-element="nav" data-kt-stepper-action="step">
                                <h3 class="stepper-title">Personal Data</h3>
                            </div>
                            <div class="stepper-item" data-kt-stepper-element="nav" data-kt-stepper-action="step">
                                <h3 class="stepper-title">Account</h3>
                            </div>
                        </div>
                        <form class="py-5 mx-auto mw-600px w-100" novalidate="novalidate" id="kt_add_employee_form"
                            enctype="multipart/form-data">
                            <div class="current" data-kt-stepper-element="content">
                                <div class="w-100">
                                    <div class="mb-10 fv-row">
                                        <x-atoms.label class="mb-3" value="Full Name" />
                                        <x-atoms.input name="name" class="form-control-lg" placeholder="Full Name" />
                                    </div>
                                    <div class="mb-10 fv-row">
                                        <x-atoms.label class="mb-3" value="Date of Birth" />
                                        <x-atoms.input name="dob" id="add-dob-picker" class="form-control-lg"
                                            placeholder="Date of Birth" />
                                    </div>
                                    <div class="mb-10 fv-row">
                                        <x-atoms.label class="mb-3" value="City" />
                                        <x-atoms.input name="city" class="form-control-lg" placeholder="City" />
                                    </div>
                                </div>
                            </div>
                            <div data-kt-stepper-element="content">
                                <div class="w-100">
                                    <div class="fv-row mb-7">
                                        <label class="mb-5 d-block fw-semibold fs-6">Profile Image</label>
                                        <style>
                                            .image-input-placeholder {
                                                background-image: url('assets/media/svg/files/blank-image.svg');
                                            }

                                            [data-bs-theme="dark"] .image-input-placeholder {
                                                background-image: url('assets/media/svg/files/blank-image-dark.svg');
                                            }
                                        </style>
                                        <div class="image-input image-input-outline image-input-placeholder image-input-empty"
                                            data-kt-image-input="true">
                                            <div class="image-input-wrapper w-125px h-125px"
                                                style="background-image: none;">
                                            </div>
                                            <label
                                                class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                                data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                                title="Change Profile Image">
                                                <x-atoms.icon icon="pencil" path="2" size="7" />
                                                <input type="file" id="add_employee_image" name="image"
                                                    accept=".png, .jpg, .jpeg" />
                                                <input type="hidden" name="avatar_remove" />
                                            </label>
                                            <span
                                                class="shadow remove-add-image btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                                data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                                title="Cancel Profile Image">
                                                <x-atoms.icon icon="cross" path="2" />
                                            </span>
                                            <span
                                                class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                                data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                                title="Remove Profile Image">
                                                <x-atoms.icon icon="cross" path="2" />
                                            </span>
                                        </div>
                                        <div class="form-text">Allowed file types: png, jpg, jpeg (max: 1MB)</div>
                                    </div>
                                    <div class="fv-row mb-7">
                                        <x-atoms.label class="fs-6" value="Email" />
                                        <x-atoms.input id="add_employee_email" name="email" class="mb-3 mb-lg-0"
                                            placeholder="Email" />
                                    </div>
                                    <div class="fv-row mb-7">
                                        <x-atoms.label class="mb-2 fw-semibold fs-6" value="Password" />
                                        <x-atoms.input type="password" id="add_employee_password" name="password"
                                            class="mb-3 form-control form-control-solid mb-lg-0" placeholder="Password"
                                            required />
                                    </div>
                                </div>
                            </div>
                            <div class="pt-5 d-flex flex-stack">
                                <div class="mr-2">
                                    <button type="button" class="btn btn-lg btn-light-primary me-3"
                                        data-kt-stepper-action="previous">
                                        <i class="ki-duotone ki-arrow-left fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Back
                                    </button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-lg btn-primary me-3"
                                        data-kt-stepper-action="submit" data-url="{{ route('api.employees.store') }}">
                                        <span class="indicator-label">Submit
                                            <i class="ki-duotone ki-arrow-right fs-3 ms-2 me-0">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i></span>
                                        <span class="indicator-progress">Please wait...
                                            <span class="align-middle spinner-border spinner-border-sm ms-2"></span></span>
                                    </button>
                                    <button type="button" class="btn btn-lg btn-primary"
                                        data-kt-stepper-action="next">Continue
                                        <i class="ki-duotone ki-arrow-right fs-4 ms-1 me-0">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal Add Employee-->

    <!--begin::Modal Edit Employee-->
    <div class="modal fade" id="kt_modal_edit_employee" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1">
        <div class="modal-dialog mw-750px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Edit Pegawai</h2>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <x-atoms.icon icon="cross" path="2" size="1" />
                    </div>
                </div>
                <div class="modal-body scroll-y">
                    <div class="stepper stepper-links d-flex flex-column" id="kt_edit_employee_stepper">
                        <div class="stepper-nav">
                            <div class="stepper-item current" data-kt-stepper-element="nav"
                                data-kt-stepper-action="step">
                                <h3 class="stepper-title">Personal Data</h3>
                            </div>
                            <div class="stepper-item" data-kt-stepper-element="nav" data-kt-stepper-action="step">
                                <h3 class="stepper-title">Account</h3>
                            </div>
                        </div>
                        <form class="py-5 mx-auto mw-600px w-100" novalidate="novalidate" id="kt_edit_employee_form"
                            enctype="multipart/form-data">
                            <div class="current" data-kt-stepper-element="content">
                                <div class="w-100">
                                    <div class="mb-10 fv-row">
                                        <x-atoms.label class="mb-3" value="Full Name" />
                                        <x-atoms.input name="name" class="form-control-lg" placeholder="Full Name" />
                                    </div>
                                    <div class="mb-10 fv-row me-4">
                                        <x-atoms.label class="mb-3" value="Date of Birth" />
                                        <x-atoms.input name="dob" id="edit-dob-picker" class="form-control-lg"
                                            placeholder="Date of Birth" />
                                    </div>
                                    <div class="mb-10 fv-row">
                                        <x-atoms.label class="mb-3" value="City" />
                                        <x-atoms.input name="city" class="form-control-lg" placeholder="City" />
                                    </div>
                                </div>
                            </div>
                            <div data-kt-stepper-element="content">
                                <div class="w-100">
                                    <div class="fv-row mb-7">
                                        <label class="mb-5 d-block fw-semibold fs-6">Profile Image</label>
                                        <style>
                                            .image-input-placeholder {
                                                background-image: url('assets/media/svg/files/blank-image.svg');
                                            }

                                            [data-bs-theme="dark"] .image-input-placeholder {
                                                background-image: url('assets/media/svg/files/blank-image-dark.svg');
                                            }
                                        </style>
                                        <div class="image-input image-input-outline image-input-placeholder image-input-empty"
                                            data-kt-image-input="true">
                                            <div class="image-input-wrapper w-125px h-125px"
                                                style="background-image: none;">
                                            </div>
                                            <label
                                                class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                                data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                                title="Change Profile Image">
                                                <x-atoms.icon icon="pencil" path="2" size="7" />
                                                <input type="file" id="edit_employee_image" name="image"
                                                    accept=".png, .jpg, .jpeg" />
                                                <input type="hidden" name="avatar_remove" />
                                            </label>
                                            <span
                                                class="shadow remove-edit-image btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                                data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                                title="Cancel Profile Image">
                                                <x-atoms.icon icon="cross" path="2" />
                                            </span>
                                            <span
                                                class="shadow btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body"
                                                data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                                title="Remove Profile Image">
                                                <x-atoms.icon icon="cross" path="2" />
                                            </span>
                                        </div>
                                        <div class="form-text">Allowed file types: png, jpg, jpeg (max: 1MB)</div>
                                    </div>
                                    <div class="fv-row mb-7">
                                        <x-atoms.label class="fs-6" value="Email" />
                                        <x-atoms.input id="edit_employee_email" name="email" class="mb-3 mb-lg-0"
                                            placeholder="Email" />
                                    </div>
                                    <div class="fv-row mb-7">
                                        <x-atoms.label class="mb-2 fw-semibold fs-6" value="Password" :required="false" />
                                        <x-atoms.input type="password" id="edit_employee_password" name="password"
                                            class="mb-3 form-control form-control-solid mb-lg-0" placeholder="Password"
                                            required />
                                    </div>
                                </div>
                            </div>
                            <div class="pt-5 d-flex flex-stack">
                                <div class="mr-2">
                                    <button type="button" class="btn btn-lg btn-light-primary me-3"
                                        data-kt-stepper-action="previous">
                                        <i class="ki-duotone ki-arrow-left fs-4 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Back
                                    </button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-lg btn-primary me-3"
                                        data-kt-stepper-action="submit">
                                        <span class="indicator-label">Submit
                                            <i class="ki-duotone ki-arrow-right fs-3 ms-2 me-0">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i></span>
                                        <span class="indicator-progress">Please wait...
                                            <span class="align-middle spinner-border spinner-border-sm ms-2"></span></span>
                                    </button>
                                    <button type="button" class="btn btn-lg btn-primary"
                                        data-kt-stepper-action="next">Continue
                                        <i class="ki-duotone ki-arrow-right fs-4 ms-1 me-0">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal Edit Employee-->

    @push('scripts')
        <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
        <script src="{{ asset('assets/js/custom/apps/employees/table.js') }}"></script>
        <script src="{{ asset('assets/js/custom/apps/employees/add.js') }}"></script>
        <script src="{{ asset('assets/js/custom/apps/employees/edit.js') }}"></script>
    @endpush
@endsection
