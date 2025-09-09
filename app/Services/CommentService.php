<?php

namespace App\Services;

use App\Repositories\Interfaces\CommentRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    protected $commentRepository;

    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function getCommentsByPost($postId)
    {
        return $this->commentRepository->allByPost($postId);
    }

    public function addComment($postId, $commentText)
    {
        $data = [
            'post_id' => $postId,
            'user_id' => Auth::id(),
            'comment_text' => $commentText, // ✅ fixed field name
        ];

        return $this->commentRepository->create($data);
    }

    public function updateComment($id, $commentText)
    {
        $comment = $this->commentRepository->find($id);

        if ($comment->user_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return $this->commentRepository->update($id, ['comment_text' => $commentText]); // ✅ fixed field name
    }

    public function deleteComment($id)
    {
        $comment = $this->commentRepository->find($id);

        if ($comment->user_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return $this->commentRepository->delete($id);
    }
}
