@extends('components.blog-page-wrapper')

@section('content')
@foreach(json_decode($posts) as $post)
@include('components.blog-post-item', ["post" => $post])
@endforeach

<nav class="blog-pagination" aria-label="Pagination">
    @if(@$prevPageLink)
    <a class="btn btn-outline-secondary" href="{{ $prevPageLink }}">Newer</a>
    @endif

    @if(@$nextPageLink)
    <a class="btn btn-outline-primary" href="{{ $nextPageLink }}">Older</a>
    @endif

</nav>
@endsection