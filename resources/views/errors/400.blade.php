@extends('errors.minimal')

@section('title', __('Bad Request'))
@section('code', '400')
@section('message', __('The server could not understand the request due to invalid syntax or a missing parameter.'))
@section('illustration')
    <img src="assets/media/errors/400-error.png" class="mw-100 mh-300px theme-light-show" alt="" />
    <img src="assets/media/errors/400-error.png" class="mw-100 mh-300px theme-dark-show" alt="" />
@endsection
