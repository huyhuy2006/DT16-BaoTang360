<?php
namespace App\Policies;
use App\Models\User;
use App\Models\Tour;
class TourPolicy { public function update(User $user, Tour $tour): bool{return $user->isEditor();} public function delete(User $user, Tour $tour): bool{return $user->isEditor();}}
