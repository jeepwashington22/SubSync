<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoUploadDebugTest extends TestCase
{
    use RefreshDatabase;

    public function test_oversized_photo_behavior(): void
    {
        Storage::fake('public');
        /** @var User $user */
        $user = User::factory()->create();

        // The request is a PATCH and the file must exceed the 10MB validator limit.
        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo' => UploadedFile::fake()->create('big.jpg', 20000, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors('profile_photo');
    }
}
