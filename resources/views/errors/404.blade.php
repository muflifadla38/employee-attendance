@extends('errors.minimal')

@section('title', __('Page Not Found'))
@section('code', '404')
@section('message', __('The requested page could not be found. Please check the URL or go back to the homepage.'))
@section('illustration')
    <img src="assets/media/errors/404-error.png" class="mw-100 mh-300px theme-light-show" alt="" />
    <img src="assets/media/errors/404-error.png" class="mw-100 mh-300px theme-dark-show" alt="" />
@endsection
