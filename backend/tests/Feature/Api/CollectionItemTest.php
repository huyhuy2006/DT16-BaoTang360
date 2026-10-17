<?php

namespace Tests\Feature\Api;

use App\Models\Artifact;
use App\Models\CollectionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_luu_hien_vat_vao_bo_suu_tap(): void
    {
        $user = User::factory()->create();
        $artifact = Artifact::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/collection-items', ['artifact_id' => $artifact->id]);

        $response->assertStatus(201);
        $this->assertDatabaseCount('collection_items', 1);
    }

    public function test_luu_trung_lap_khong_tao_ban_ghi_moi(): void
    {
        $user = User::factory()->create();
        $artifact = Artifact::factory()->create();

        $this->actingAs($user, 'sanctum')->postJson('/api/collection-items', ['artifact_id' => $artifact->id]);
        $this->actingAs($user, 'sanctum')->postJson('/api/collection-items', ['artifact_id' => $artifact->id]);

        $this->assertDatabaseCount('collection_items', 1);
    }

    public function test_khach_chua_dang_nhap_khong_the_luu(): void
    {
        $artifact = Artifact::factory()->create();

        $this->postJson('/api/collection-items', ['artifact_id' => $artifact->id])->assertStatus(401);
    }

    public function test_khong_the_xoa_muc_cua_nguoi_khac(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $item = CollectionItem::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($other, 'sanctum')->deleteJson('/api/collection-items/' . $item->id);

        $response->assertStatus(403);
    }

    public function test_bo_suu_tap_rong_tra_ve_mang_rong(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/collection-items');

        $response->assertStatus(200)->assertJsonCount(0);
    }

    public function test_xoa_muc_hop_le_thanh_cong(): void
    {
        $user = User::factory()->create();
        $item = CollectionItem::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user, 'sanctum')->deleteJson('/api/collection-items/' . $item->id)->assertStatus(200);
        $this->assertDatabaseCount('collection_items', 0);
    }
}
