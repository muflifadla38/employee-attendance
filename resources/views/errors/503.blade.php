@extends('errors.minimal')

@section('title', __('Service Unavailable'))
@section('code', '503')
@section('message', __('The service is temporarily unavailable. Please try again later.'))
@section('illustration')
    <img src="assets/media/errors/503-error.png" class="mw-100 mh-300px theme-light-show" alt="" />
    <img src="assets/media/errors/503-error.png" class="mw-100 mh-300px theme-dark-show" alt="" />
@endsection
