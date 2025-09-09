<?php

namespace App\Repositories\Interfaces;

interface CommentRepositoryInterface
{
    public function allByPost($postId);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
