<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'post_comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_post',
        'content',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    protected $appends = ['readable_created_at', 'readable_updated_at'];


    public function post_parent()
    {
        return $this->belongsTo(Post::class, "id_post");
    }

    public function comment_parent()
    {
        return $this->belongsTo($this, "id_parent_comment");
    }

    public function comment_children()
    {
        return $this->hasMany($this, "id_parent_comment");
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    public function updated_by_user()
    {
        return $this->belongsTo(User::class, "updated_by");
    }

    public function getReadableCreatedAtAttribute()
    {
        return $this->created_at->format('F, d Y H:i:s');;
    }

    public function getReadableUpdatedAtAttribute()
    {
        if ($this->updated_at instanceof Carbon) {
            return $this->updated_at->format('F, d Y H:i:s');
        }
        return null;
    }
}
