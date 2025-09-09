<?php

namespace App\Repositories\Interfaces;

interface LikeRepositoryInterface
{
    public function allLikesForPost($postId);
    public function find($id);
    public function create(array $data);
    public function delete($id);
    public function firstOrCreate(array $data);
}
