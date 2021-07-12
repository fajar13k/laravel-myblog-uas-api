<?php

namespace App\Libraries;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use stdClass;

class BlogService
{
    const API_BASE = "https://blog-api.stmik-amikbandung.ac.id/api/v2/blog/_table/";
    const API_KEY = "ef9187e17dce5e8a5da6a5d16ba760b75cadd53d19601a16713e5b7c4f683e1b";

    const ARTICLES_CACHE_STRING     = "zarticle";
    const COMMENT_CACHE_STRING      = "comment";
    const AUTHOR_CACHE_STRING      = "author";

    private $client;

    public function __construct()
    {
        $this->client = new Client([
            "base_uri" => self::API_BASE,
            "headers"  => [
                "X-DreamFactory-API-Key" => self::API_KEY
            ]
        ]);
    }

    //Start of Private area
    private function makeRequest($type, $feature, $data)
    {
        try {
            $dataModel = [
                "resource" => []
            ];

            $dataModel["resource"][] = $data;

            switch (strtoupper($type)) {
                case "POST":
                    $req = $this->client->post($feature, [
                        "json" => $dataModel
                    ]);
                    break;
                case "PUT":
                    $req = $this->client->put($feature, [
                        "json" => $dataModel
                    ]);
                    break;
                case "DELETE":
                    $req = $this->client->delete($feature);
                    return true;
                default:
                    throw new InvalidArgumentException();
            }

            $apiResponse = json_decode($req->getBody())->resource;
            $newId = $apiResponse[0]->id;

            return $newId;
        } catch (Exception $e) {
            return false;
        }
    }

    private function postSomething($feature, $data)
    {
        return $this->makeRequest("post", $feature, $data);
    }

    private function putSomething($feature, $data)
    {
        return $this->makeRequest("put", $feature, $data);
    }

    private function deleteSomething($feature, $data)
    {
        return $this->makeRequest("delete", $feature, $data);
    }
    //End of Private area

    public function forgetAll()
    {
        Cache::forget(self::ARTICLES_CACHE_STRING);
        Cache::forget(self::COMMENT_CACHE_STRING);
        Cache::forget(self::AUTHOR_CACHE_STRING);
    }

    public function fetchArticles()
    {
        $data = Cache::get(self::ARTICLES_CACHE_STRING, function () {
            try {
                $reqData = $this->client->get("articles");
                $res = json_decode($reqData->getBody())->resource;

                foreach ($res as $item) {
                    $item->carbonCreatedAt = Carbon::parse($item->created_at);
                    $item->created_by = $item->author;
                    $item->created_by_user = $this->getAuthor($item->author);
                    $item->readable_created_at = $item->carbonCreatedAt->format('F, d Y H:i:s');
                    $item->post_preview = \Illuminate\Support\Str::limit(strip_tags($item->content), 500);
                }

                $data = collect($res);

                $res = $data->sortByDesc("carbonCreatedAt")->toArray();

                Cache::add(self::ARTICLES_CACHE_STRING, $res);
                return $res;
            } catch (Exception $e) {
                return [];
            }
        });

        return $data;
    }

    public function fetchPaginatedArticles($perPage = 5, $page = 1, $date = null, $userId = null)
    {
        $data = collect($this->fetchArticles());

        $nextPageNumber = $page + 1;
        $prevPageNumber = $page - 1;

        $skipPosts = $perPage * ($page - 1);

        $data = $data->slice($skipPosts, $perPage);

        if (!is_null($date)) {
            $dateSplit = explode("-", $date);

            $year = $dateSplit[0];
            $month = $dateSplit[1];

            $data = $data->whereYear('created_at', '=', $year)
                ->whereMonth('created_at', '=', $month);
        } elseif (!is_null($userId)) {
            $data = $data->where("author", $userId);
        }

        $result = new stdClass();

        $result->posts = $data;
        $result->prevPageNumber = $prevPageNumber;
        $result->nextPageNumber = $nextPageNumber;

        return $result;
    }

    public function fetchArticleArchives()
    {
        $data = collect($this->fetchArticles());

        $data = $data->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('Y-m');
        });

        return $data;
    }

    public function getAuthor($id)
    {
        $data = Cache::get(self::AUTHOR_CACHE_STRING . "_$id", function () use (&$id) {
            try {
                $reqData = $this->client->get("authors/$id");
                $res = json_decode($reqData->getBody());

                Cache::add(self::AUTHOR_CACHE_STRING . "_$id", $res);
                return $res;
            } catch (Exception $e) {
                return [$e];
            }
        });

        return $data;
    }

    public function fetchAutors()
    {
        $data = Cache::get("cache_authors", function () {
            try {
                $reqData = $this->client->get("authors");
                $res = json_decode($reqData->getBody());

                Cache::add("cache_authors", $res);
                return $res;
            } catch (Exception $e) {
                return [$e];
            }
        });

        return $data;
    }

    public function getArticle($id, $withComment = true)
    {
        $reqData = $this->client->get("articles/$id");
        $res = json_decode($reqData->getBody());

        $res->created_by = $res->author;
        $res->created_by_user = $this->getAuthor($res->author);
        $res->readable_created_at = Carbon::parse($res->created_at)->format('F, d Y H:i:s');

        if ($withComment) {
            $res->comments = $this->getComment($res->id);
        }

        return $res;
    }

    public function fetchComments()
    {
        $data = Cache::get(self::COMMENT_CACHE_STRING, function () {
            try {
                $reqData = $this->client->get("comments");
                $res = json_decode($reqData->getBody())->resource;

                foreach ($res as $item) {
                    $item->carbonCreatedAt = Carbon::parse($item->created_at);
                    $item->readable_created_at = $item->carbonCreatedAt->format('F, d Y H:i:s');
                }

                $data = collect($res);

                $res = $data->sortByDesc("carbonCreatedAt")->toArray();

                Cache::add(self::COMMENT_CACHE_STRING, $res);
                return $res;
            } catch (Exception $e) {
                return [];
            }
        });

        return $data;
    }

    public function getComment($post_id)
    {
        $data = collect($this->fetchComments());

        $data = $data->where("article", $post_id);

        return $data->toArray();
    }

    public function addArticle($userId, $title, $content)
    {
        $data = [
            "author" => $userId,
            "title" => $title,
            "content" => $content
        ];

        $result = $this->postSomething("articles", $data);

        if ($result) {
            Cache::forget(self::ARTICLES_CACHE_STRING);
        }

        return $result;
    }

    public function editArticle($articleId, $title, $content)
    {
        $data = [
            "id" => $articleId,
            "title" => $title,
            "content" => $content
        ];

        $result = $this->putSomething("articles", $data);

        if ($result) {
            Cache::forget(self::ARTICLES_CACHE_STRING);
        }

        return $result;
    }

    public function deleteComment($commentId, $forgetCommentCache = true) {
        $result = $this->deleteSomething("comments/$commentId", []);

        if ($forgetCommentCache) {
            Cache::forget(self::ARTICLES_CACHE_STRING);
        }

        return $result;
    }

    public function deleteArticle($articleId)
    {
        $data = [
            "id" => $articleId,
        ];

        //First, we have to delete all the comments.
        $comments = $this->getComment($articleId);

        foreach($comments as $item) {
            try {
                $result = $this->deleteComment($item->id, false);

                if(!$result) throw new Exception();
            } catch(Exception $e) {
                return false;
            }
        }

        //Then we can delete the article itself.
        $result = $this->deleteSomething("articles/$articleId", []);

        if ($result) {
            Cache::forget(self::COMMENT_CACHE_STRING);
            Cache::forget(self::ARTICLES_CACHE_STRING);
        }

        return $result;
    }

    public function addComment($userId, $articleId, $content)
    {
        $data = [
            "author" => $userId,
            "article" => $articleId,
            "content" => $content
        ];

        $result = $this->postSomething("comments", $data);

        if ($result) {
            Cache::forget(self::COMMENT_CACHE_STRING);
        }

        return $result;
    }

    public function editComment($commentId, $content)
    {
        $data = [
            "id" => $commentId,
            "content" => $content
        ];

        $result = $this->putSomething("comments", $data);

        if ($result) {
            Cache::forget(self::COMMENT_CACHE_STRING);
        }

        return $result;
    }
}
