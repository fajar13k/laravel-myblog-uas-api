<article class="blog-post">
    <hr>
    <form action="{{ $form_url }}" method="post">
        @csrf
        <div class="mb-3">
            <label for="post_title1" class="form-label">Post title</label>
            <input name="post_title" class="form-control" id="post_title1" placeholder="Post title here" value="{{ @$post->title ?? '' }}">
        </div>
        <div class="mb-3">
            <label for="post_content1" class="form-label">Example textarea</label>
            <textarea name="post_content" class="form-control" id="post_content1" rows="10">{{ @$post->content ?? "" }}</textarea>
        </div>
        <div class="btn-group py-3" role="group">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>

</article>