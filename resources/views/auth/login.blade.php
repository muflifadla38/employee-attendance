@extends('layouts.auth')
@push('token')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <style>
        body {
            background-image: url('/assets/media/auth/bg10.jpeg');
        }

        [data-bs-theme="dark"] body {
            background-image: url('/assets/media/auth/bg10-dark.jpeg');
        }
    </style>
    <div class="d-flex flex-column flex-lg-row flex-column-fluid">
        <div class="d-flex flex-lg-row-fluid">
            <div class="p-10 pb-0 d-flex flex-column flex-center pb-lg-10 w-100">
                <img class="mx-auto mb-10 theme-light-show mw-100 w-150px w-lg-300px mb-lg-20"
                    src="{{ asset('assets/media/auth/agency.png') }}" alt="Login Image" />
                <h1 class="text-center text-gray-800 fs-2qx fw-bold mb-7">Fast, Efficient and Productive</h1>
                <div class="text-center text-gray-600 fs-base fw-semibold">In this kind of post,
                    the blogger introduces a person they’ve
                    interviewed
                    <br />and provides some background information about
                    the interviewee and their
                    <br />work following this is a transcript of the interview.
                </div>
            </div>
        </div>
        <div class="p-12 d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end">
            <div class="p-10 bg-body d-flex flex-column flex-center rounded-4 w-md-600px">
                <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px">
                    <div class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20">
                        <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form"
                            data-kt-redirect-url="{{ route('dashboard.index') }}" method="POST"
                            action="{{ route('login.index') }}">
                            @csrf
                            <div class="text-center mb-11">
                                <h1 class="mb-3 text-dark fw-bolder">Login</h1>
                            </div>
                            <div class="mb-8 fv-row">
                                <input type="email" placeholder="Email" name="email" autocomplete="off"
                                    class="bg-transparent form-control" required />
                            </div>
                            <div class="mb-8 fv-row">
                                <input type="password" placeholder="Password" name="password" aupinttocomplete="off"
                                    class="bg-transparent form-control" required />
                            </div>
                            <div class="mb-10 d-grid">
                                <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                                    <span class="indicator-label">Sign In</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="align-middle spinner-border spinner-border-sm ms-2"></span></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/js/custom/authentication/login.js') }}"></script>
    @endpush
@endsection
