<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
class SceneFactory extends Factory { public function definition(): array { return ['space_id'=>1,'name'=>fake()->sentence(2),'panorama_image'=>'panoramas/demo.jpg','initial_pitch'=>0,'initial_yaw'=>0,'initial_hfov'=>100,'order_index'=>0]; } }
