<?php

namespace Tests\Feature\Api;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->withHeaders([
            'accept' => 'application/json',
        ]);
    }

    public function test_a_post_can_be_stored(): void
    {
        $this->withoutExceptionHandling();

        $file = File::create('my_image.jpg');

        $data = [
            'title' => 'Some title',
            'description' => 'Description',
            'image' => $file,
        ];

        $response = $this->post('/api/posts', $data);

        $this->assertDatabaseCount('posts', 1);

        $post = Post::first();

        $this->assertEquals($data['title'], $post->title);
        $this->assertEquals($data['description'], $post->description);
        $this->assertEquals("images/" . $file->hashName(), $post->image_url);

        $response->assertJson([
            'id' => $post->id,
            'title' => $post->title,
            'description' => $post->description,
            'image_url' => $post->image_url,
        ]);
    }

    
    public function test_attribute_title_is_file_for_storing_post(): void
    {
        $data = [
            'title' => 'Title',
            'description' => 'Description',
            'image' => 'dsad'
        ];

        $response = $this->post('/api/posts', $data);

        $response->assertStatus(422);
        $response->assertInvalid('image');
        $response->assertJsonValidationErrors([
            'image' => 'The image field must be a file'
        ]);
    }

    public function test_a_post_can_be_updated(): void
    {
        $this->withoutExceptionHandling();
        
        $post = Post::factory()->create();
        $file = File::create('image.jpg');

        $data = [
            'title' => 'Title edited',
            'description' => 'Description edited',
            'image' => $file
        ];

        $response = $this->patch('/api/posts/' . $post->id, $data);
        $response->assertJson([
            'id' => $post->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'image_url' => 'images/' . $file->hashName(),
        ]);
    }

// https://youtu.be/leaXsWyfQRs?t=7000 продолжение


}
