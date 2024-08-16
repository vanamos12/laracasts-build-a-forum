<?php

use App\Models\Comment;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('requires authentification', function(){
    $this->put(route('comments.update', Comment::factory()->create()))
        ->assertRedirect(route('login'));
});

it('can update a comment', function(){
    $comment = Comment::factory()->create(['body' => 'This is the old body']);

    $newBody = 'This is the new body';

    actingAs($comment->user)
        ->put(route('comments.update', $comment), [
            'body' => $newBody
        ]);

    $this->assertDatabaseHas(Comment::class, [
        'id' => $comment->id,
        'body' => $newBody
    ]);
});

it('redirects to the post show page', function(){
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->put(route('comments.update', $comment), [
            'body' => 'This is the new body'
        ])
        ->assertRedirect(route('posts.show', $comment->post_id));
});

it('redirects to the correct page of comments', function(){
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->put(route('comments.update', ['comment'=>$comment, 'page'=>2]), [
            'body' => 'This is the new body'
        ])
        ->assertRedirect(route('posts.show', ['post' => $comment->post_id, 'page' => 2]));
});

it('cannot update a comment from another user', function (){
    $comment = Comment::factory()->create();

    actingAs(User::factory()->create())
        ->put(route('comments.update', ['comment'=>$comment, 'page'=>2]), [
            'body' => 'This is the new body'
        ])
        ->assertForbidden();
});

it('requires a valid body', function($body){
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->put(route('comments.update', ['comment'=>$comment, 'page'=>2]), [
            'body' => $body
        ])
        ->assertInvalid('body');
})->with([
    null,
    true,
    1,
    1.5,
    str_repeat('a', 2501)
]);
