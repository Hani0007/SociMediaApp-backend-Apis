<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\Interfaces\PostRepositoryInterface;

class PostRepository implements PostRepositoryInterface
{
    public function all()
    {
        return Post::with(['user', 'media'])->orderBy('created_at', 'desc')->get();
    }

    public function find($id)
    {
        return Post::with(['user', 'media', 'comments', 'likes'])->findOrFail($id);
    }

    public function create(array $data)
    {
        $post = Post::create([
            'user_id' => $data['user_id'],
            'description' => $data['description'],
        ]);

        if (!empty($data['media_ids'])) {
            $post->media()->attach($data['media_ids']);
        }

        return $post->load('media');
    }

    public function update($id, array $data)
    {
        $post = Post::findOrFail($id);
        $post->update($data);
        return $post;
    }

    public function delete($id)
    {
        $post = Post::findOrFail($id);
        return $post->delete();
    }
}
