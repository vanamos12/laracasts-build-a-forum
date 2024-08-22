<?php

use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function(){
    $this->validData = [
        'title' => 'Hello World!',
        'body' => 'This is my first post!'.str_repeat('b', 150)
    ];
});

it('requires authentification', function (){
    $this->post(route('posts.store'))
        ->assertRedirect(route('login'));
});


it('stores a post', function (){
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('posts.store'), $this->validData);

    $this->assertDatabaseHas(Post::class, [
        ...$this->validData,
        'user_id' => $user->id
    ]);
});

it('redirects to the post page', function(){
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('posts.store'), $this->validData)
        ->assertRedirect(route('posts.show', Post::latest('id')->first()));

});
/*
it('requires a valid title', function ($badTitle){
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('posts.store'), [
            ...$this->validData,
            'title' => $badTitle
        ])
        ->assertInvalid('title');
})->with([
    null,
    true,
    1,
    1.5,
    str_repeat('a', 121),
    str_repeat('a', 9)
]);


it('requires a valid body', function ($badBody){
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('posts.store'), [
            ...$this->validData,
            'body' => $badBody
        ])
        ->assertInvalid('body');
})->with([
    null,
    true,
    1,
    1.5,
    str_repeat('a', 10_001),
    str_repeat('a', 99)
]);
*/

it('requires a valid data', function (array $badData, array|string $errors){
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('posts.store'), [
            ...$this->validData,
            ...$badData
        ])
        ->assertInvalid($errors);
})->with([
    [['title'=>null], 'title'],
    [['title'=>true], 'title'],
    [['title'=>1], 'title'],
    [['title'=>1.5], 'title'],
    [['title'=>str_repeat('a', 121)], 'title'],
    [['title'=>str_repeat('a', 9)], 'title'],
    [['body'=>null], 'body'],
    [['body'=>true], 'body'],
    [['body'=>1], 'body'],
    [['body'=>1.5], 'body'],
    [['body'=>str_repeat('', 10_001)], 'body'],
    [['body'=>str_repeat('a', 99)], 'body']
]);