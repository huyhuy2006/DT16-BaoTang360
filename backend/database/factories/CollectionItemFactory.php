<?php
namespace Database\Factories;
use App\Models\Artifact;
use Illuminate\Database\Eloquent\Factories\Factory;
class CollectionItemFactory extends Factory { public function definition(): array { return ['user_id'=>1,'artifact_id'=>Artifact::factory(),'note'=>null]; } }
