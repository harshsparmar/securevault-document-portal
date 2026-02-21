<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class DocumentViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_document_list(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer']);

        $response = $this->actingAs($viewer)
            ->get(route('documents.index'));

        $response->assertStatus(200);
        $response->assertViewIs('documents.index');
    }

    public function test_guest_cannot_view_document_list(): void
    {
        $response = $this->get(route('documents.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_document_detail(): void
    {
        Storage::fake('local');

        $uploader = User::factory()->create(['role' => 'uploader']);
        $viewer = User::factory()->create(['role' => 'viewer']);

        // Create a fake text file in storage
        Storage::disk('local')->put('private/documents/test.txt', 'Hello World');

        $document = Document::create([
            'user_id'       => $uploader->id,
            'original_name' => 'test.txt',
            'mime_type'     => 'text/plain',
            'storage_path'  => 'private/documents/test.txt',
        ]);

        // Use a signed URL (as the routes now require it)
        $signedUrl = URL::temporarySignedRoute(
            'documents.show',
            now()->addMinutes(30),
            ['document' => $document->id]
        );

        $response = $this->actingAs($viewer)->get($signedUrl);

        $response->assertStatus(200);
        $response->assertViewIs('documents.show');
        $response->assertSee('Hello World');
    }

    public function test_authenticated_user_can_preview_pdf(): void
    {
        Storage::fake('local');

        $uploader = User::factory()->create(['role' => 'uploader']);
        $viewer = User::factory()->create(['role' => 'viewer']);

        // Create a minimal fake PDF
        Storage::disk('local')->put('private/documents/test.pdf', '%PDF-1.4 fake content');

        $document = Document::create([
            'user_id'       => $uploader->id,
            'original_name' => 'report.pdf',
            'mime_type'     => 'application/pdf',
            'storage_path'  => 'private/documents/test.pdf',
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'documents.preview',
            now()->addMinutes(30),
            ['document' => $document->id]
        );

        $response = $this->actingAs($viewer)
            ->get($signedUrl, ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'inline; filename="report.pdf"');
    }

    public function test_preview_response_has_security_headers(): void
    {
        Storage::fake('local');

        $user = User::factory()->create(['role' => 'viewer']);

        Storage::disk('local')->put('private/documents/test.txt', 'Hello');

        $document = Document::create([
            'user_id'       => User::factory()->create(['role' => 'uploader'])->id,
            'original_name' => 'test.txt',
            'mime_type'     => 'text/plain',
            'storage_path'  => 'private/documents/test.txt',
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'documents.preview',
            now()->addMinutes(30),
            ['document' => $document->id]
        );

        $response = $this->actingAs($user)
            ->get($signedUrl, ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertStatus(200);
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Cache-Control');
    }

    public function test_guest_cannot_preview_document(): void
    {
        $document = Document::factory()->create();

        $signedUrl = URL::temporarySignedRoute(
            'documents.preview',
            now()->addMinutes(30),
            ['document' => $document->id]
        );

        $response = $this->get($signedUrl, ['X-Requested-With' => 'XMLHttpRequest']);

        $response->assertRedirect(route('login'));
    }

    public function test_direct_browser_access_to_preview_is_blocked(): void
    {
        $user = User::factory()->create(['role' => 'viewer']);
        $document = Document::factory()->create();

        $signedUrl = URL::temporarySignedRoute(
            'documents.preview',
            now()->addMinutes(30),
            ['document' => $document->id]
        );

        // Without AJAX header — simulates typing URL directly in browser
        $response = $this->actingAs($user)->get($signedUrl);

        $response->assertStatus(403);
    }

    public function test_unsigned_url_returns_403(): void
    {
        $user = User::factory()->create(['role' => 'viewer']);
        $document = Document::factory()->create();

        // Use a plain route (unsigned) — should be rejected
        $response = $this->actingAs($user)
            ->get('/documents/' . $document->id);

        $response->assertStatus(403);
    }

    public function test_expired_signed_url_returns_403(): void
    {
        Storage::fake('local');

        $user = User::factory()->create(['role' => 'viewer']);

        Storage::disk('local')->put('private/documents/test.txt', 'Content');

        $document = Document::create([
            'user_id'       => User::factory()->create(['role' => 'uploader'])->id,
            'original_name' => 'test.txt',
            'mime_type'     => 'text/plain',
            'storage_path'  => 'private/documents/test.txt',
        ]);

        // Generate a URL that expired 1 minute ago
        $expiredUrl = URL::temporarySignedRoute(
            'documents.show',
            now()->subMinute(),
            ['document' => $document->id]
        );

        $response = $this->actingAs($user)->get($expiredUrl);

        $response->assertStatus(403);
    }

    public function test_document_uses_uuid(): void
    {
        Storage::fake('local');

        $uploader = User::factory()->create(['role' => 'uploader']);

        $document = Document::create([
            'user_id'       => $uploader->id,
            'original_name' => 'test.pdf',
            'mime_type'     => 'application/pdf',
            'storage_path'  => 'private/documents/test.pdf',
        ]);

        // UUID is 36 characters (with hyphens)
        $this->assertEquals(36, strlen($document->id));
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/',
            $document->id
        );
    }

    public function test_no_raw_file_path_in_show_view(): void
    {
        Storage::fake('local');

        $user = User::factory()->create(['role' => 'viewer']);

        Storage::disk('local')->put('private/documents/test.txt', 'Content');

        $document = Document::create([
            'user_id'       => User::factory()->create(['role' => 'uploader'])->id,
            'original_name' => 'test.txt',
            'mime_type'     => 'text/plain',
            'storage_path'  => 'private/documents/test.txt',
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'documents.show',
            now()->addMinutes(30),
            ['document' => $document->id]
        );

        $response = $this->actingAs($user)->get($signedUrl);

        $response->assertStatus(200);

        // Ensure no storage paths leak into the HTML
        $response->assertDontSee('private/documents/');
        $response->assertDontSee('storage/app/');
    }
}
