<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_uploader_can_upload_pdf(): void
    {
        $uploader = User::factory()->create(['role' => 'uploader']);

        $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($uploader)
            ->post(route('documents.store'), ['document' => $file]);

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('documents', 1);
        $this->assertDatabaseHas('documents', [
            'original_name' => 'document.pdf',
            'mime_type'     => 'application/pdf',
            'user_id'       => $uploader->id,
        ]);
    }

    public function test_uploader_can_upload_txt(): void
    {
        $uploader = User::factory()->create(['role' => 'uploader']);

        $file = UploadedFile::fake()->create('readme.txt', 512, 'text/plain');

        $response = $this->actingAs($uploader)
            ->post(route('documents.store'), ['document' => $file]);

        $response->assertRedirect(route('documents.index'));
        $this->assertDatabaseCount('documents', 1);
    }

    public function test_viewer_cannot_upload(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer']);

        $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($viewer)
            ->post(route('documents.store'), ['document' => $file]);

        $response->assertStatus(403);
        $this->assertDatabaseCount('documents', 0);
    }

    public function test_viewer_cannot_access_upload_form(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer']);

        $response = $this->actingAs($viewer)
            ->get(route('documents.create'));

        $response->assertStatus(403);
    }

    public function test_invalid_mime_type_rejected(): void
    {
        $uploader = User::factory()->create(['role' => 'uploader']);

        $file = UploadedFile::fake()->create('script.exe', 1024, 'application/x-msdownload');

        $response = $this->actingAs($uploader)
            ->post(route('documents.store'), ['document' => $file]);

        $response->assertSessionHasErrors('document');
        $this->assertDatabaseCount('documents', 0);
    }

    public function test_guest_cannot_upload(): void
    {
        $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

        $response = $this->post(route('documents.store'), ['document' => $file]);

        $response->assertRedirect(route('login'));
    }

    public function test_file_stored_in_private_storage(): void
    {
        $uploader = User::factory()->create(['role' => 'uploader']);

        $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

        $this->actingAs($uploader)
            ->post(route('documents.store'), ['document' => $file]);

        $document = Document::first();
        $this->assertStringStartsWith('private/documents/', $document->storage_path);
        Storage::disk('local')->assertExists($document->storage_path);
    }
}
