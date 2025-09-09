<?php

namespace App\Repositories;

use App\Models\Like;
use App\Repositories\Interfaces\LikeRepositoryInterface;

class LikeRepository implements LikeRepositoryInterface
{
    public function allLikesForPost($postId)
    {
        return Like::where('post_id', $postId)->get();
    }

    public function find($id)
    {
        return Like::findOrFail($id);
    }

    public function create(array $data)
    {
        return Like::create($data);
    }

    public function delete($id)
    {
        $like = Like::findOrFail($id);
        return $like->delete();
    }

    public function firstOrCreate(array $data)
    {
        return Like::firstOrCreate($data);
    }
}
