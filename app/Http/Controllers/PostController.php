<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    //
    public function index(){
        return inertia('Posts/Index', [
            'posts' => PostResource::collection(Post::all())
        ]);
    }
}
