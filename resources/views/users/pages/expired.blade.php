@extends('users.layouts.master')
@section('title', 'Link Expired')

@section('content')
<div class="container text-center mt-5">
    <i class="fa-solid fa-triangle-exclamation fa-3x text-danger mb-3"></i>
    <h3>Link Expired</h3>
    <p>{{ $message }}</p>
    <a href="/" class="btn btn-outline-secondary mt-3">
        <i class="fa-solid fa-upload"></i> Upload Again
    </a>
</div>
@endsection
