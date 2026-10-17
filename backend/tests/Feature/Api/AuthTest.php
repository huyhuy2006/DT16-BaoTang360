<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_dang_ky_hop_le_tra_ve_token_va_bam_mat_khau(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Nguyen Van A',
            'email' => 'a@vidu.vn',
            'password' => 'MatKhau@123',
            'password_confirmation' => 'MatKhau@123',
        ]);

        $response->assertStatus(201)->assertJsonStructure(['user', 'token']);
        $this->assertDatabaseHas('users', ['email' => 'a@vidu.vn']);
        $this->assertNotEquals('MatKhau@123', User::first()->password);
    }

    public function test_dang_ky_email_trung_bi_tu_choi(): void
    {
        User::factory()->create(['email' => 'trung@vidu.vn']);

        $response = $this->postJson('/api/register', [
            'name' => 'B', 'email' => 'trung@vidu.vn',
            'password' => 'MatKhau@123', 'password_confirmation' => 'MatKhau@123',
        ]);

        $response->assertStatus(422);
    }

    public function test_dang_ky_mat_khau_qua_ngan_bi_tu_choi(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'C', 'email' => 'c@vidu.vn',
            'password' => '1234567', 'password_confirmation' => '1234567',
        ]);

        $response->assertStatus(422);
    }

    public function test_dang_nhap_dung_tra_ve_token(): void
    {
        User::factory()->create(['email' => 'd@vidu.vn', 'password' => bcrypt('MatKhau@123')]);

        $response = $this->postJson('/api/login', ['email' => 'd@vidu.vn', 'password' => 'MatKhau@123']);

        $response->assertStatus(200)->assertJsonStructure(['user', 'token']);
    }

    public function test_dang_nhap_sai_mat_khau_bi_tu_choi(): void
    {
        User::factory()->create(['email' => 'e@vidu.vn', 'password' => bcrypt('MatKhau@123')]);

        $response = $this->postJson('/api/login', ['email' => 'e@vidu.vn', 'password' => 'sai']);

        $response->assertStatus(401);
    }

    public function test_goi_me_khong_kem_token_bi_tu_choi(): void
    {
        $this->getJson('/api/me')->assertStatus(401);
    }

    public function test_visitor_khong_the_tao_khong_gian(): void
    {
        $visitor = User::factory()->create(['role' => 'visitor']);

        $response = $this->actingAs($visitor, 'sanctum')->postJson('/api/spaces', ['name' => 'Phòng X']);

        $response->assertStatus(403);
    }

    public function test_token_sau_dang_xuat_khong_con_hieu_luc(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)->postJson('/api/logout')->assertStatus(200);
        $this->withHeader('Authorization', 'Bearer ' . $token)->getJson('/api/me')->assertStatus(401);
    }
}
