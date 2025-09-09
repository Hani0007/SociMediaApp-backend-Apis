<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Repositories\Interfaces\CommentRepositoryInterface;

class CommentRepository implements CommentRepositoryInterface
{
    protected $model;

    public function __construct(Comment $comment)
    {
        $this->model = $comment;
    }

    public function allByPost($postId)
    {
        return $this->model->where('post_id', $postId)
                           ->with('user')
                           ->orderBy('created_at', 'desc')
                           ->get();
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $comment = $this->find($id);
        $comment->update($data);
        return $comment;
    }

    public function delete($id)
    {
        $comment = $this->find($id);
        return $comment->delete();
    }
}
