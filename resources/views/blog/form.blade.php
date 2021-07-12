@extends('components.blog-page-wrapper')

@section('content')
@include('components.blog-post-item-form', ["post" => json_decode(@$post)])
@endsection