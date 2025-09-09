<?php

namespace App\Services;

use App\Repositories\Interfaces\MediaRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class MediaService
{
    protected $mediaRepository;

    public function __construct(MediaRepositoryInterface $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    public function getAllMedia()
    {
        return $this->mediaRepository->all();
    }

    public function uploadMedia($file, $mediaType)
    {
        // Store file in storage/app/public/media
        $path = $file->store('media', 'public');
        $url = asset('storage/' . $path);

        return $this->mediaRepository->create([
            'media_type' => $mediaType,
            'url' => $url,
        ]);
    }

    public function attachMediaToPost($mediaId, $postId)
    {
        return $this->mediaRepository->attachToPost($mediaId, $postId);
    }

    public function getMedia($id)
    {
        return $this->mediaRepository->find($id);
    }

    public function deleteMedia($mediaId, $postId)
    {
        if (!$this->mediaRepository->existsForPost($mediaId, $postId)) {
            abort(404, 'Media not found for this post');
        }

        $media = $this->mediaRepository->find($mediaId);

        // Delete file from storage
        $filePath = str_replace(asset('storage/'), '', $media->url);
        Storage::disk('public')->delete($filePath);

        // Delete DB record
        return $this->mediaRepository->delete($mediaId);
    }
}
