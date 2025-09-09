<?php

namespace App\Repositories;

use App\Models\Media;
use App\Repositories\Interfaces\MediaRepositoryInterface;

class MediaRepository implements MediaRepositoryInterface
{
    protected $model;

    public function __construct(Media $media)
    {
        $this->model = $media;
    }

    public function all()
    {
        return $this->model::all();
    }

    public function find($id)
    {
        return $this->model::findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model::create($data);
    }

    public function delete($id)
    {
        $media = $this->find($id);
        return $media->delete();
    }

    public function attachToPost($mediaId, $postId)
    {
        $media = $this->find($mediaId);
        $media->posts()->syncWithoutDetaching($postId);
        return $media;
    }

    public function existsForPost($mediaId, $postId)
    {
        $media = $this->find($mediaId);
        return $media->posts()->where('posts.id', $postId)->exists();
    }
}
