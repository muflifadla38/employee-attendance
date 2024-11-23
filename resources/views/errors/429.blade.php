@extends('errors.minimal')

@section('title', __('Too Many Requests'))
@section('code', '429')
@section('message', __('You have made too many requests in a short period. Please wait a while before trying again.'))
@section('illustration')
    <img src="assets/media/errors/error.png" class="mw-100 mh-300px theme-light-show" alt="" />
    <img src="assets/media/errors/error.png" class="mw-100 mh-300px theme-dark-show" alt="" />
@endsection
