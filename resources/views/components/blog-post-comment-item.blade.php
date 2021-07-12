<div class="alert alert-secondary mx-3" role="alert">
    <b>{{ $comment->author }}</b> comments at {{ $comment->readable_created_at }}
    <hr>
    <p>{!! $comment->content !!}</p>
</div>