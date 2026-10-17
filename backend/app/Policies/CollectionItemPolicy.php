<?php
namespace App\Policies;
use App\Models\User;
use App\Models\CollectionItem;
class CollectionItemPolicy { public function delete(User $user, CollectionItem $item): bool{return $item->user_id === $user->id;}}
