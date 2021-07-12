<?php

namespace App\Http\Controllers;

use App\Libraries\BlogService;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    private $perPage = 5;
    private $currentUser = 1;
    private $parameters;

    public function __construct()
    {
        //get route parameters from the URL.
        $this->parameters = request()->route()->parameters();
    }

    public function refresh()
    {
        $blog = new BlogService();

        $blog->forgetAll();

        return redirect()->to("/");
    }

    public function index()
    {
        $rest = new BlogService();

        $data = [];

        $page = $this->parameters['page'] ?? 1;
        $date = $this->parameters['date'] ?? null;
        $userId = $this->parameters['userId'] ?? null;

        $articles = $rest->fetchPaginatedArticles($this->perPage, $page, $date, $userId);

        if (!is_null($date)) { //If date is not null, then it's archive.
            $data = array_merge($data, [
                "caption" => "Showing posts at $date, page $page",
                "nextPageLink" => $articles->nextPageNumber > 0 ? url()->to("archive/{$date}/page/{$articles->nextPageNumber}") : null,
                "prevPageLink" => $articles->prevPageNumber > 0 ? url()->to("archive/{$date}/page/{$articles->prevPageNumber}") : null,
            ]);
        } elseif (!is_null($userId)) { //If date is not null, then it's shows posts by user.
            $user = $rest->getAuthor($userId);

            $data = array_merge($data, [
                "caption" => "Showing post(s) by {$user->name}, page $page",
                "nextPageLink" => $articles->nextPageNumber > 0 ? url()->to("u/{$userId}/page/{$articles->nextPageNumber}") : null,
                "prevPageLink" => $articles->prevPageNumber > 0 ? url()->to("u/{$userId}/page/{$articles->prevPageNumber}") : null,
            ]);
        } else {
            $data = array_merge($data, [
                "caption" => $page > 1 ? "Showing posts page $page" : null,
                "nextPageLink" => $articles->nextPageNumber > 0 ? url()->to("page/{$articles->nextPageNumber}") : null,
                "prevPageLink" => $articles->prevPageNumber > 0 ? url()->to("page/{$articles->prevPageNumber}") : url()->to("/"),
            ]);
        }

        $archives = $rest->fetchArticleArchives();

        $data = array_merge($data, [
            "posts" => json_encode($articles->posts),
            "archives" => $archives,
            "success" => session()->get("success"),
            "error" => session()->get("error"),
        ]);

        return view('blog.home', $data);
    }

    public function view()
    {
        $rest = new BlogService();

        $post_id = $this->parameters['post_id'];

        $post = $rest->getArticle($post_id);


        $data = [
            "success" => session()->get("success"),
            "error" => session()->get("error"),
            "isMyPost" => $post->author == $this->currentUser,
            "post" => json_encode($post),
        ];

        return view('blog.view', $data);
    }

    public function form()
    {
        $data = [];

        $rest = new BlogService();

        $post_id = $this->parameters['post_id'] ?? null; //If uid is not null, then edit the post, otherwise we create a new post.

        if (!is_null($post_id)) { //If uis is not null, we edit the post.
            $post = $rest->getArticle($post_id, false);

            $data = array_merge($data, [
                "post" => json_encode($post),
                "caption" => "Edit post",
                "form_url" => url()->to('view/' . $post->id . '/do_save_post')
            ]);
        } else { //Otherwise, we create a new post.
            $data = array_merge($data, [
                "caption" => "New post",
                "form_url" => url()->to('do_save_post')
            ]);
        }

        return view('blog.form', $data);
    }

    public function do_comment()
    {
        $rest = new BlogService();

        $post_id = $this->parameters['post_id'];

        $myComment = request()->post("my_comment");

        $result = $rest->addComment($this->currentUser, $post_id, $myComment);

        if ($result) {
            return redirect()->to("view/$post_id")->with(["success" => "Berhasil membuat komentar."]);
        } else {
            return redirect()->to("view/$post_id")->with(["error" => "Gagal membuat komentar."]);
        }
    }

    public function do_save_post()
    {
        $rest = new BlogService();

        $title = request()->post("post_title");
        $content = request()->post("post_content");

        $post_id = $this->parameters['post_id'] ?? null; //If uid is not null, then edit the post, otherwise we create a new post.

        if (!is_null($post_id)) { //If uis is not null, we edit the post.
            $result = $rest->editArticle($post_id, $title, $content);

            if ($result) {
                return redirect()->to("view/$result")->with(["success" => "Berhasil menyunting artikel."]);
            } else {
                return redirect()->to("/")->with(["error" => "Gagal menyunting artikel."]);
            }
        } else { //Otherwise, we create a new post.
            $result = $rest->addArticle($this->currentUser, $title, $content);

            if ($result) {
                return redirect()->to("view/$result")->with(["success" => "Berhasil membuat artikel."]);
            } else {
                return redirect()->to("/")->with(["error" => "Gagal membuat artikel."]);
            }
        }
    }

    public function delete()
    {
        $rest = new BlogService();

        $post_id = $this->parameters['post_id'] ?? null; //If uid is not null, then edit the post, otherwise we create a new post.

        $result = $rest->deleteArticle($post_id);

        if ($result) {
            return redirect()->to("/")->with(["success" => "Berhasil menghapus artikel."]);
        } else {
            return redirect()->to("/")->with(["error" => "Gagal menghapus artikel."]);
        }
    }
}
