@extends('errors.minimal')

@section('title', __('Gateway Timeout'))
@section('code', '504')
@section('message', __('The server, while acting as a gateway or proxy, did not receive a timely response from the
    upstream server.'))
@section('illustration')
    <img src="assets/media/errors/504-error.png" class="mw-100 mh-300px theme-light-show" alt="" />
    <img src="assets/media/errors/504-error.png" class="mw-100 mh-300px theme-dark-show" alt="" />
@endsection
