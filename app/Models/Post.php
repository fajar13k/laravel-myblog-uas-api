<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content', 
        'created_by',
        'created_at',
        'updated_at',
        'updated_by'
    ];

    protected $appends = ['readable_created_at', 'readable_updated_at', 'post_preview'];

    public function comments()
    {
        return $this->hasMany(PostComment::class, "id_post")->orderBy('created_at', 'DESC');
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

    public function getPostPreviewAttribute()
    {
        return \Illuminate\Support\Str::limit($this->content, 500);
    }
}
