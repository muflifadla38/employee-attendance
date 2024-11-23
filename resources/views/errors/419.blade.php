@extends('errors.minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('The page has expired due to inactivity. Please refresh the page and try again.'))
@section('illustration')
    <img src="assets/media/errors/error.png" class="mw-100 mh-300px theme-light-show" alt="" />
    <img src="assets/media/errors/error.png" class="mw-100 mh-300px theme-dark-show" alt="" />
@endsection
