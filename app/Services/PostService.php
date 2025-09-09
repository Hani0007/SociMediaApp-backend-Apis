<?php

namespace App\Services;

use App\Repositories\Interfaces\PostRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class PostService
{
    protected $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getAllPosts()
    {
        return $this->postRepository->all();
    }

    public function getPost($id)
    {
        return $this->postRepository->find($id);
    }

    public function createPost(array $data)
    {
        $data['user_id'] = Auth::id();
        return $this->postRepository->create($data);
    }

    public function updatePost($id, array $data)
    {
        // Only allow user to update own posts
        $post = $this->postRepository->find($id);
        if ($post->user_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return $this->postRepository->update($id, $data);
    }

    public function deletePost($id)
    {
        $post = $this->postRepository->find($id);
        if ($post->user_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return $this->postRepository->delete($id);
    }
}
