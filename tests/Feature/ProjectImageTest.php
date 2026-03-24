<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectImageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_can_create_project_with_multiple_images()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->postJson('/api/projects', [
            'title' => 'Nouveau Projet',
            'description' => 'Description test',
            'status' => 'en_cours',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'image_path' => UploadedFile::fake()->image('main.jpg'),
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
                UploadedFile::fake()->image('image2.jpg'),
            ]
        ]);

        $response->assertStatus(201);
        $project = Project::first();
        
        $this->assertNotNull($project->image_path);
        $this->assertCount(2, $project->images);
        $this->assertStringContainsString('projects/', $project->images[0]);
    }

    public function test_can_add_images_to_existing_project()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::factory()->create([
            'auteur' => $admin->id,
            'images' => ['projects/old1.jpg']
        ]);

        $response = $this->actingAs($admin)->putJson("/api/projects/{$project->id}", [
            'images' => [
                UploadedFile::fake()->image('new.jpg'),
            ]
        ]);

        $response->assertStatus(200);
        $project->refresh();
        $this->assertCount(2, $project->images);
        $this->assertEquals('projects/old1.jpg', $project->images[0]);
    }

    public function test_can_delete_specific_image()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $project = Project::factory()->create([
            'auteur' => $admin->id,
            'images' => ['projects/image1.jpg', 'projects/image2.jpg']
        ]);

        $response = $this->actingAs($admin)->deleteJson("/api/projects/{$project->id}/images", [
            'image_path' => 'projects/image1.jpg'
        ]);

        $response->assertStatus(200);
        $project->refresh();
        $this->assertCount(1, $project->images);
        $this->assertEquals('projects/image2.jpg', $project->images[0]);
    }
}
