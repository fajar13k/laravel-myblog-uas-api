<article class="blog-post">
    <a href="{{ url()->to('view/'.$post->id) }}">
        <h2 class="blog-post-title">{{ $post->title }}</h2>
    </a>

    <p class="blog-post-meta">Created at {{ $post->readable_created_at }} by <a href="{{ url()->to('u/'.$post->created_by.'') }}">{{ $post->created_by_user->name }}</a></p>

    <p>
        {{$post->post_preview}}
    </p>
</article>