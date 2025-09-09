<?php

namespace App\Repositories\Interfaces;

interface MediaRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function delete($id);
    public function attachToPost($mediaId, $postId);
    public function existsForPost($mediaId, $postId);
}
