<?php

namespace Tests\Feature\Api;

use App\Models\Scene;
use App\Models\Space;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_tao_lo_trinh_voi_it_nhat_2_diem_nhin_thanh_cong(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $space = Space::factory()->create(['created_by' => $admin->id]);
        $scenes = Scene::factory()->count(3)->create(['space_id' => $space->id]);

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/tours', [
            'name' => 'Hành trình gốm cổ',
            'scene_ids' => $scenes->pluck('id')->all(),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseCount('tour_steps', 3);
    }

    public function test_tao_lo_trinh_chi_1_diem_nhin_bi_tu_choi(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $space = Space::factory()->create(['created_by' => $admin->id]);
        $scene = Scene::factory()->create(['space_id' => $space->id]);

        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/tours', [
            'name' => 'Lộ trình quá ngắn',
            'scene_ids' => [$scene->id],
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('tours', 0);
    }

    public function test_that_bai_giua_chung_khong_de_lai_du_lieu_mo_coi(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $space = Space::factory()->create(['created_by' => $admin->id]);
        $scenes = Scene::factory()->count(2)->create(['space_id' => $space->id]);

        // scene_id không tồn tại chen vào giữa danh sách -> exists rule chặn ngay ở validate,
        // đảm bảo DB::transaction trong TourController không bao giờ tạo Tour mồ côi.
        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/tours', [
            'name' => 'Lộ trình lỗi',
            'scene_ids' => [$scenes[0]->id, 999999],
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('tours', 0);
        $this->assertDatabaseCount('tour_steps', 0);
    }
}
