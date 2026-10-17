<?php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
class ArtifactFactory extends Factory { public function definition(): array { return ['name'=>fake()->sentence(3),'description'=>fake()->paragraph(),'period'=>'Thế kỷ XV','material'=>'Gốm','image'=>null]; } }
