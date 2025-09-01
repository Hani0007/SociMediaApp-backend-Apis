<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    /**
     * Fields allowed for mass assignment
     */
    protected $fillable = [
        'user_id',
        'description',
    ];

    /**
     * A post belongs to a user (author)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A post can have many comments
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * A post can have many likes
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * Many-to-many: Users who liked this post (via likes pivot)
     */
    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'likes', 'post_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Many-to-many: Media files attached to this post (via post_media pivot)
     */
    public function media()
    {
        return $this->belongsToMany(Media::class, 'post_media', 'post_id', 'media_id')
            ->withTimestamps();
    }
}
