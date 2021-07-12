@extends('components.blog-page-wrapper')

@section('content')
@include('components.blog-post-item-detail', ["post" => json_decode($post)])
@endsection