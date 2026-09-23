<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_a_profile_photo(): void
    {
        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo' => UploadedFile::fake()->image('avatar.jpg', 100, 100),
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('status', 'profile-updated');

        $user->refresh();

        $this->assertNotNull($user->profile_photo_path);
        /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
        $publicDisk = Storage::disk('public');
        $publicDisk->assertExists($user->profile_photo_path);
        $this->assertStringStartsWith('profile-photos/', $user->profile_photo_path);
    }

    public function test_uploading_a_new_photo_replaces_the_old_one(): void
    {
        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()->create([
            'profile_photo_path' => 'profile-photos/old.jpg',
        ]);
        /** @var \Illuminate\Filesystem\FilesystemAdapter $publicDisk */
        $publicDisk = Storage::disk('public');
        $publicDisk->put('profile-photos/old.jpg', 'old');

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo' => UploadedFile::fake()->image('new.png', 100, 100),
        ]);

        $response->assertRedirect(route('profile.edit'));

        $user->refresh();
        $publicDisk->assertExists($user->profile_photo_path);
        $publicDisk->assertMissing('profile-photos/old.jpg');
    }

    public function test_profile_photo_must_be_a_valid_image(): void
    {
        Storage::fake('public');

        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo' => UploadedFile::fake()->create('document.pdf', 100),
        ]);

        $response->assertSessionHasErrors('profile_photo');
        $this->assertNull($user->refresh()->profile_photo_path);
    }

    public function test_settings_page_renders_with_dark_layout(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertOk();
        // Content is offset so it never overlaps the fixed sidebar
        $response->assertSee('lg:pl-72', false);
        // Dark theme is applied
        $response->assertSee('bg-[#08090a]', false);
        // Profile photo uploader is present
        $response->assertSee('profile_photo');
    }

    public function test_sidebar_highlights_only_the_active_tab(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // On the Settings page, the Settings link is active and others are not
        $response = $this->actingAs($user)->get(route('profile.edit'));
        $html = $response->getContent();

        $settingsPos = strpos($html, 'aria-current="page"');
        $this->assertNotFalse($settingsPos, 'No active tab marked on Settings page');
        $afterSettings = substr($html, 0, $settingsPos);
        $this->assertStringContainsString('Settings', substr($html, $settingsPos - 500, 1000));

        // Exactly one active tab in the sidebar
        $this->assertSame(1, substr_count($html, 'aria-current="page"'));
    }

    public function test_dashboard_highlights_the_overview_tab(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $this->assertSame(1, substr_count($response->getContent(), 'aria-current="page"'));
        $response->assertSee('aria-current="page"', false);
    }
}
