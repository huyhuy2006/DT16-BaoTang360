<?php

namespace Database\Seeders;

use App\Models\Artifact;
use App\Models\CollectionItem;
use App\Models\GuestbookEntry;
use App\Models\Favorite;
use App\Models\Feedback;
use App\Models\Hotspot;
use App\Models\Language;
use App\Models\Narration;
use App\Models\Scene;
use App\Models\SceneLink;
use App\Models\Space;
use App\Models\Tour;
use App\Models\TourStep;
use App\Models\User;
use App\Models\VisitLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Protect an existing local database from accidental duplicate demo seeding.
        // For a fresh test/demo database, migrations create the users table first.
        if (User::query()->exists()) {
            $this->command?->warn('Đã có người dùng trong CSDL; bỏ qua seeder demo để tránh tạo dữ liệu trùng.');
            return;
        }

        DB::transaction(function (): void {
            $admin = User::create(['name' => 'Quản trị viên ĐT-16', 'email' => 'admin@example.test', 'password' => 'MatKhau@123', 'role' => 'admin']);
            $editor = User::create(['name' => 'Biên tập viên ĐT-16', 'email' => 'editor@example.test', 'password' => 'MatKhau@123', 'role' => 'editor']);
            $visitors = collect();
            for ($i = 1; $i <= 8; $i++) {
                $visitors->push(User::create(['name' => "Khách tham quan {$i}", 'email' => "visitor{$i}@example.test", 'password' => 'MatKhau@123', 'role' => 'visitor']));
            }

            $languages = collect([
                Language::create(['code' => 'vi', 'name' => 'Tiếng Việt']),
                Language::create(['code' => 'en', 'name' => 'English']),
            ]);

            $spaces = collect();
            for ($i = 1; $i <= 5; $i++) {
                $spaces->push(Space::create([
                    'name' => "Không gian trưng bày {$i}",
                    'slug' => "khong-gian-trung-bay-{$i}",
                    'description' => "Không gian mẫu {$i} của hệ thống Bảo tàng ảo ĐT-16.",
                    'cover_image' => 'spaces/cover-' . $i . '.jpg',
                    'created_by' => $i % 2 ? $admin->id : $editor->id,
                ]));
            }

            $scenes = collect();
            foreach ($spaces as $space) {
                for ($j = 1; $j <= 5; $j++) {
                    $scenes->push(Scene::create([
                        'space_id' => $space->id,
                        'name' => "{$space->name} - Điểm nhìn {$j}",
                        'panorama_image' => 'panoramas/scene-' . str_pad((string)(($j - 1) % 5 + 1), 2, '0', STR_PAD_LEFT) . '/preview.jpg',
                        'initial_pitch' => (($j * 3) % 20) - 10,
                        'initial_yaw' => (($j * 45) % 360) - 180,
                        'initial_hfov' => 100,
                        'order_index' => $j - 1,
                    ]));
                }
            }

            $artifacts = collect();
            $materials = ['Gốm', 'Đồng', 'Gỗ', 'Đá', 'Vải'];
            $periods = ['Thế kỷ XV', 'Thế kỷ XVII', 'Thế kỷ XIX', 'Đầu thế kỷ XX', 'Hiện đại'];
            for ($i = 1; $i <= 40; $i++) {
                $artifacts->push(Artifact::create([
                    'name' => "Hiện vật ĐT-16 #{$i}",
                    'description' => "Mô tả thuyết minh mẫu cho hiện vật {$i}. Dữ liệu mô phỏng phục vụ phát triển và kiểm thử.",
                    'period' => $periods[($i - 1) % count($periods)],
                    'material' => $materials[($i - 1) % count($materials)],
                    'image' => 'artifacts/demo-' . (($i - 1) % 20 + 1) . '.jpg',
                ]));
            }

            $hotspots = collect();
            foreach ($scenes as $index => $scene) {
                for ($j = 0; $j < ($index < 15 ? 2 : 1); $j++) {
                    $artifact = $artifacts->get(($index * 2 + $j) % $artifacts->count());
                    $hotspots->push(Hotspot::create([
                        'scene_id' => $scene->id,
                        'artifact_id' => $artifact->id,
                        'pitch' => ($j === 0 ? -5 : 8) + (($index % 4) * 2),
                        'yaw' => (($index * 31) + ($j * 90)) % 360 - 180,
                        'title' => $artifact->name,
                    ]));
                }
            }

            // Liên kết các điểm nhìn kế tiếp trong từng không gian.
            foreach ($spaces as $space) {
                $ordered = $scenes->where('space_id', $space->id)->values();
                for ($i = 0; $i < $ordered->count() - 1; $i++) {
                    SceneLink::create([
                        'from_scene_id' => $ordered[$i]->id,
                        'to_scene_id' => $ordered[$i + 1]->id,
                        'pitch' => 0,
                        'yaw' => 0,
                        'label' => 'Sang điểm nhìn tiếp theo',
                    ]);
                }
            }

            foreach ($hotspots as $hotspot) {
                $artifact = $artifacts->firstWhere('id', $hotspot->artifact_id);
                foreach ($languages as $lang) {
                    Narration::create([
                        'hotspot_id' => $hotspot->id,
                        'artifact_id' => $artifact?->id,
                        'language_id' => $lang->id,
                        'audio_path' => 'audio/' . $lang->code . '/artifact-demo.wav',
                        'transcript' => ($lang->code === 'vi' ? 'Thuyết minh tiếng Việt cho ' : 'English narration for ') . ($artifact?->name ?? 'hiện vật') . '.',
                    ]);
                }
            }

            $tours = collect();
            for ($i = 1; $i <= 5; $i++) {
                $tour = Tour::create([
                    'name' => "Lộ trình chủ đề {$i}",
                    'theme' => ['Gốm cổ', 'Di sản', 'Lịch sử', 'Nghệ thuật', 'Dân tộc học'][$i % 5],
                    'description' => "Lộ trình tham quan tự sự mẫu {$i}, gồm 5 điểm dừng.",
                    'cover_image' => 'tours/tour-' . $i . '.jpg',
                    'created_by' => $i % 2 ? $admin->id : $editor->id,
                ]);
                $tours->push($tour);
                $start = (($i - 1) * 5) % $scenes->count();
                for ($step = 0; $step < 5; $step++) {
                    $scene = $scenes[($start + $step) % $scenes->count()];
                    TourStep::create([
                        'tour_id' => $tour->id,
                        'scene_id' => $scene->id,
                        'order_index' => $step,
                        'note' => 'Điểm dừng ' . ($step + 1) . ' của lộ trình ' . $i . '.',
                    ]);
                }
            }

            for ($i = 1; $i <= 10; $i++) {
                GuestbookEntry::create([
                    'user_id' => $visitors->get(($i - 1) % $visitors->count())->id,
                    'display_name' => "Khách {$i}",
                    'message' => "Trải nghiệm tham quan ảo lần {$i} rất hữu ích.",
                ]);
            }

            for ($i = 0; $i < 10; $i++) {
                CollectionItem::create([
                    'user_id' => $visitors->get($i % $visitors->count())->id,
                    'artifact_id' => $artifacts->get($i)->id,
                    'note' => 'Hiện vật được lưu trong bộ sưu tập cá nhân.',
                ]);
            }

            // A few demo favorites and approved/pending reviews exercise the personal
            // collection and moderation screen. These records are synthetic.
            for ($i = 0; $i < 5; $i++) {
                Favorite::firstOrCreate([
                    'user_id' => $visitors->get($i)->id,
                    'target_type' => 'tour',
                    'target_id' => $tours->get($i)->id,
                ]);
                Feedback::firstOrCreate([
                    'user_id' => $visitors->get($i)->id,
                    'target_type' => $i % 2 === 0 ? 'tour' : 'artifact',
                    'target_id' => $i % 2 === 0 ? $tours->get($i)->id : $artifacts->get($i)->id,
                ], [
                    'rating' => 4 + ($i % 2),
                    'comment' => 'Đánh giá mô phỏng để kiểm thử quy trình duyệt nội dung.',
                    'status' => $i % 2 === 0 ? 'approved' : 'pending',
                ]);
            }

            $actions = ['view_scene', 'click_hotspot', 'play_narration', 'start_tour', 'finish_tour'];
            for ($i = 0; $i < 45; $i++) {
                $scene = $scenes->get($i % $scenes->count());
                $hotspot = $hotspots->get($i % $hotspots->count());
                VisitLog::create([
                    'user_id' => $visitors->get($i % $visitors->count())->id,
                    'session_id' => 'demo-session-' . (($i % 20) + 1),
                    'scene_id' => $scene->id,
                    'hotspot_id' => in_array($actions[$i % count($actions)], ['click_hotspot', 'play_narration'], true) ? $hotspot->id : null,
                    'action' => $actions[$i % count($actions)],
                    'duration_seconds' => 20 + ($i % 180),
                    'created_at' => now()->subMinutes(100 - $i),
                ]);
            }

            $this->command?->info('ĐT-16 demo dataset: ' . (
                User::count() + Language::count() + Space::count() + Scene::count() + SceneLink::count() + Artifact::count() + Hotspot::count() + Narration::count() + Tour::count() + TourStep::count() + GuestbookEntry::count() + CollectionItem::count() + VisitLog::count()
            ) . ' bản ghi trong CSDL.');
        });
    }
}
