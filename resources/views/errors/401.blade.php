@extends('errors.minimal')

@section('title', __('Unauthorized'))
@section('code', '401')
@section('message', __('You are not authorized to access this page. Please log in or provide valid credentials.'))
@section('illustration')
    <img src="assets/media/errors/401-error.png" class="mw-100 mh-300px theme-light-show" alt="" />
    <img src="assets/media/errors/401-error.png" class="mw-100 mh-300px theme-dark-show" alt="" />
@endsection
