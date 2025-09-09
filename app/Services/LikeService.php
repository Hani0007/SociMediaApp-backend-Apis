<?php

namespace App\Services;

use App\Repositories\Interfaces\LikeRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class LikeService
{
    protected $likeRepository;

    public function __construct(LikeRepositoryInterface $likeRepository)
    {
        $this->likeRepository = $likeRepository;
    }

    public function likePost($postId)
    {
        $userId = Auth::id();
        if (!$userId) {
            throw new \Exception("Unauthenticated", 401);
        }

        return $this->likeRepository->firstOrCreate([
            'user_id' => $userId,
            'post_id' => $postId
        ]);
    }

    public function unlikePost($id)
    {
        $like = $this->likeRepository->find($id);

        if ($like->user_id != Auth::id()) {
            throw new \Exception("Unauthorized to delete this like", 403);
        }

        return $this->likeRepository->delete($id);
    }

    public function getLikesCount($postId)
    {
        return $this->likeRepository->allLikesForPost($postId)->count();
    }
}
