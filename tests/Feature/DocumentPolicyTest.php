<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_uploader_can_upload(): void
    {
        $uploader = User::factory()->create(['role' => 'uploader']);

        $this->assertTrue($uploader->can('upload', Document::class));
    }

    public function test_viewer_cannot_upload(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer']);

        $this->assertFalse($viewer->can('upload', Document::class));
    }

    public function test_uploader_can_view_documents(): void
    {
        $uploader = User::factory()->create(['role' => 'uploader']);
        $document = Document::factory()->create();

        $this->assertTrue($uploader->can('view', $document));
        $this->assertTrue($uploader->can('viewAny', Document::class));
    }

    public function test_viewer_can_view_documents(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer']);
        $document = Document::factory()->create();

        $this->assertTrue($viewer->can('view', $document));
        $this->assertTrue($viewer->can('viewAny', Document::class));
    }

    public function test_viewer_can_preview_documents(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer']);
        $document = Document::factory()->create();

        $this->assertTrue($viewer->can('preview', $document));
    }

    public function test_uploader_role_helper(): void
    {
        $uploader = User::factory()->create(['role' => 'uploader']);
        $viewer = User::factory()->create(['role' => 'viewer']);

        $this->assertTrue($uploader->isUploader());
        $this->assertFalse($uploader->isViewer());
        $this->assertTrue($viewer->isViewer());
        $this->assertFalse($viewer->isUploader());
    }
}
