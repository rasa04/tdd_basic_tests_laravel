<?php

namespace Tests\Feature;

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

        $response = $this->post('/posts', $data);
        $response->assertOk();

        $this->assertDatabaseCount('posts', 1);

        $post = Post::first();

        $this->assertEquals($data['title'], $post->title);
        $this->assertEquals($data['description'], $post->description);
        $this->assertEquals("images/" . $file->hashName(), $post->image_url);

        Storage::disk('local')->assertExists($post->image_url);
    }

    public function test_attribute_title_is_required_for_storing_post(): void
    {   
        $data = [
            'title' => '',
            'description' => 'dasdad',
            'image' => ''
        ];

        $response = $this->post('/posts', $data);

        $response->assertRedirect();
        $response->assertInvalid('title');
    }

    public function test_attribute_title_is_file_for_storing_post(): void
    {
        $data = [
            'title' => 'Title',
            'description' => 'Description',
            'image' => 'dsad'
        ];

        $response = $this->post('/posts', $data);

        $response->assertRedirect();
        $response->assertInvalid('image');
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

        $response = $this->patch('/posts/' . $post->id, $data);
        $response->assertOk();
        
        $updatedPost = Post::first();
        $this->assertEquals($data['title'], $updatedPost->title);
        $this->assertEquals($data['description'], $updatedPost->description);
        $this->assertEquals("images/" . $file->hashName(), $updatedPost->image_url);
        $this->assertEquals($updatedPost->id, $post->id);
    }


    public function test_response_for_route_posts_index_is_view_index_with_posts()
    {
        $this->withoutExceptionHandling();
        $posts = Post::factory(10)->create();

        $response = $this->get('/posts');

        $response->assertViewIs('posts.index');
        
        $titles = $posts->pluck('title')->toArray();
        $response->assertSeeText($titles);


    }

    public function test_response_for_route_posts_show_is_view_post_show_with_single_post()
    {
        $this->withoutExceptionHandling();
        $post = Post::factory()->create();
        
        $response = $this->get('/posts/' . $post->id);

        $response->assertViewIs('posts.show');
        $response->assertSeeText('Post');
        $response->assertSeeText($post->title);
        $response->assertSeeText($post->description);
    }

    public function test_a_post_can_be_deleted_by_auth_user()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $post = Post::factory()->create();
        $response = $this->actingAs($user)->delete('/posts/' . $post->id);
        $response->assertOk();

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_a_post_can_be_deleted_by_only_auth_user()
    {
        $post = Post::factory()->create();
        $response = $this->delete('/posts/' . $post->id);

        $response->assertRedirect();
        $this->assertDatabaseCount('posts', 1);
    }








}
