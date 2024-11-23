@extends('errors.minimal')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Access to this resource is forbidden. You do not have permission to
    view this page.'))
@section('illustration')
    <img src="assets/media/errors/403-error.png" class="mw-100 mh-300px theme-light-show" alt="" />
    <img src="assets/media/errors/403-error.png" class="mw-100 mh-300px theme-dark-show" alt="" />
@endsection
