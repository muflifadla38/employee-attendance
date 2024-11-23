@extends('layouts.dashboard')

@section('content')
    <div class="card mb-6">
        <div class="card-header border-0 pt-6">
            <div class="card-title">Hello!</div>
        </div>
        <div class="card-body py-4">{{ __('You are logged in!') }}</div>
    </div>

    <div class="row g-5 g-xl-8">
        @role('admin')
            <div class="col-xl-6">
                <x-molecules.stats-card :link="route('users.index')" value="{{ $user }}" description="Jumlah User">
                    <x-atoms.icon class="text-primary fs-2x ms-n1" icon="user" path="2" size="2x" />
                </x-molecules.stats-card>
            </div>
            <div class="col-xl-6">
                <x-molecules.stats-card :link="route('employees.index')" value="{{ $employee }}" description="Jumlah Pegawai">
                    <x-atoms.icon class="text-primary fs-2x ms-n1" icon="profile-user" path="4" size="2x" />
                </x-molecules.stats-card>
            </div>
        @endrole
    </div>
@endsection
