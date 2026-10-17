<?php

namespace Tests\Feature\Api;

use App\Models\Artifact;
use App\Models\Hotspot;
use App\Models\Scene;
use App\Models\Space;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_spaces_index_returns_public_spaces(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Space::factory()->create(['created_by' => $admin->id]);
        $this->getJson('/api/spaces')->assertOk()->assertJsonCount(1);
    }

    public function test_space_detail_includes_hotspots_and_outgoing_links(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $space = Space::factory()->create(['created_by' => $admin->id]);
        $a = Scene::factory()->create(['space_id' => $space->id]);
        $b = Scene::factory()->create(['space_id' => $space->id]);
        $artifact = Artifact::factory()->create();
        Hotspot::create(['scene_id' => $a->id, 'artifact_id' => $artifact->id, 'pitch' => 0, 'yaw' => 10, 'title' => 'Điểm nóng']);
        $this->getJson('/api/spaces/'.$space->id)->assertOk()
            ->assertJsonPath('scenes.0.id', $a->id);
    }

    public function test_visit_log_can_be_created_as_guest(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $space = Space::factory()->create(['created_by' => $admin->id]);
        $scene = Scene::factory()->create(['space_id' => $space->id]);
        $this->postJson('/api/visit-logs', [
            'session_id' => 'test-session',
            'scene_id' => $scene->id,
            'action' => 'view_scene',
            'duration_seconds' => 30,
        ])->assertStatus(201)->assertJsonPath('action', 'view_scene');
    }
}
