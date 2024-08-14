<?php

use App\Models\Comment;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('requires authentification', function(){
    $this->delete(route('comments.destroy', Comment::factory()->create()))
        ->assertRedirect(route('login'));
});

it('can delete a comment', function(){
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->delete(route('comments.destroy', $comment));

    $this->assertModelMissing($comment);
});

it('redirects to the post show page', function(){
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->delete(route('comments.destroy', $comment))
        ->assertRedirect(route('posts.show', $comment->post_id));
});

it('prevents deleting a comment you didn\'t create', function () {
    $comment = Comment::factory()->create();

    actingAs(User::factory()->create())
        ->delete(route('comments.destroy', $comment))
        ->assertForbidden();
});

it('prevents deleting a comment posted over an hour ago', function () {
    $this->freezetime();

    $comment = Comment::factory()->create();

    $this->travel(1)->hour();
    
    actingAs($comment->user)
        ->delete(route('comments.destroy', $comment))
        ->assertForbidden();
});

it('redirects to the post show page with the page query parameter', function(){
    $comment = Comment::factory()->create();

    actingAs($comment->user)
        ->delete(route('comments.destroy', ['comment' => $comment, 'page' => 2]))
        ->assertRedirect(route('posts.show', ['post' => $comment->post_id, 'page' => 2 ]));
});



