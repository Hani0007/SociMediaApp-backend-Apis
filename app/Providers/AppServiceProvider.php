<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Repositories\PostRepository;
use App\Repositories\Interfaces\LikeRepositoryInterface;
use App\Repositories\LikeRepository;
use App\Repositories\Interfaces\CommentRepositoryInterface;
use App\Repositories\CommentRepository;
use App\Repositories\MediaRepository;
use App\Repositories\Interfaces\MediaRepositoryInterface;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
$this->app->bind(MediaRepositoryInterface::class, MediaRepository::class);
        $this->app->bind(
        \App\Repositories\Interfaces\CommentRepositoryInterface::class,
        \App\Repositories\CommentRepository::class
        );
        $this->app->bind(
            \App\Repositories\Interfaces\LikeRepositoryInterface::class,
            \App\Repositories\LikeRepository::class
        );

        $this->app->bind(
            \App\Repositories\Interfaces\PostRepositoryInterface::class,
            \App\Repositories\PostRepository::class
        );
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
