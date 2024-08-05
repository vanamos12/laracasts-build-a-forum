<?php

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Inertia\Testing\AssertableInertia;

it('should return the correct component', function(){
    $this->get(route('posts.index'))
        ->assertComponent('Posts/Index');
});

it('passes posts to the view', function (){
    
    $posts = Post::factory(3)->create();

    $this->get(route('posts.index'))
    ->assertHasPaginatedResource('posts', PostResource::collection($posts->reverse()));
 
});

