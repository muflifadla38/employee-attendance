@extends('errors.minimal')

@section('title', __('Internal Server Error'))
@section('message', __('There was an internal server error. Our team is working to resolve this issue.'))
@section('illustration')
    <img src="assets/media/errors/500-error.png" class="mw-100 mh-300px theme-light-show" alt="" />
    <img src="assets/media/errors/500-error.png" class="mw-100 mh-300px theme-dark-show" alt="" />
@endsection
