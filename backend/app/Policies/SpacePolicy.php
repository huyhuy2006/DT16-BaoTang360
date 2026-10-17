<?php
namespace App\Policies;
use App\Models\User;
use App\Models\Space;
class SpacePolicy { public function update(User $user, Space $space): bool{return $user->isEditor();} public function delete(User $user, Space $space): bool{return $user->isEditor();}}
