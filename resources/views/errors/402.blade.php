@extends('errors.minimal')

@section('title', __('Payment Required'))
@section('code', '402')
@section('message', __('Access to this resource requires a payment. Please complete the payment process to proceed.'))
@section('illustration')
    <img src="assets/media/errors/error.png" class="mw-100 mh-300px theme-light-show" alt="" />
    <img src="assets/media/errors/error.png" class="mw-100 mh-300px theme-dark-show" alt="" />
@endsection
