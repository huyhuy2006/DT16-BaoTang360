<?php
namespace App\Policies;
use App\Models\User;
use App\Models\Artifact;
class ArtifactPolicy { public function update(User $user, Artifact $artifact): bool{return $user->isEditor();} public function delete(User $user, Artifact $artifact): bool{return $user->isEditor();}}
