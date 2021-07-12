<article class="blog-post">
    <h2 class="blog-post-title">{{ $post->title }}</h2>

    <p class="blog-post-meta">Created at {{ $post->readable_created_at }} by <a href="{{ url()->to('u/'.$post->created_by.'') }}">{{ $post->created_by_user->name }}</a></p>

    <p>{!! $post->content !!}</p>

    @if($isMyPost)
    <div class="btn-group py-3" role="group">
        <a href="{{ url()->to('view/'.$post->id.'/edit') }}" class="btn btn-primary">Edit this post</a>
        <a href="{{ url()->to('view/'.$post->id.'/delete') }}" class="btn btn-danger">Delete this post</a>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            Comments
        </div>
        <div class="card-body">
            <form method="post" action="{{ url()->to('view/'.$post->id.'/do_comment') }}">
                @csrf
                <textarea class="form-control" name="my_comment" placeholder="What do you think?" rows="3" required></textarea>
                <button type="submit" class="btn btn-primary mt-2 mb-2">Submit</button>
            </form>
            <hr>
        </div>

        @if(empty($post->comments))
        <div class="alert alert-warning mx-3" role="alert">
            No comment(s).
        </div>
        @else
        @foreach($post->comments as $comment)
        @include('components.blog-post-comment-item', ["comment" => $comment])
        @endforeach
        @endif

    </div>

</article>