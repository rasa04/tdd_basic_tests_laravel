<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\Post\StoreRequest;
use App\Http\Requests\Api\Post\UpdateRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;

class PostController extends Controller
{
    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        if (!empty($data['image'])) {
            $path = Storage::disk('local')->put('/images', $data['image']);
            $data['image_url'] = $path;
        }
        unset($data['image']);
        $post = Post::create($data);
        return PostResource::make($post)->resolve();
    }
    
    
    public function update(UpdateRequest $request, Post $post)
    {
        $data = $request->validated();
        if (!empty($data['image'])) {
            $path = Storage::disk('local')->put('/images', $data['image']);
            $data['image_url'] = $path;
        }
        unset($data['image']);
        $post->update($data);
        return PostResource::make($post)->resolve();
    }
}
