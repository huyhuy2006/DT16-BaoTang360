<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
class SpaceFactory extends Factory { public function definition(): array { $name=fake()->sentence(3); return ['name'=>$name,'slug'=>Str::slug($name).'-'.fake()->unique()->numberBetween(1000,99999),'description'=>fake()->paragraph(),'cover_image'=>null,'created_by'=>1]; } }
