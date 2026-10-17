<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active'];
    protected $hidden = ['password', 'remember_token'];

    protected static function booted(): void
    {
        static::creating(function (self $user) {
            $user->role ??= 'visitor';
        });
    }

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_active' => 'boolean'];
    }

    public function isAdmin(): bool { return $this->role === 'admin'; }
    public function isEditor(): bool { return in_array($this->role, ['admin', 'editor'], true); }
    public function collectionItems() { return $this->hasMany(CollectionItem::class); }
    public function guestbookEntries() { return $this->hasMany(GuestbookEntry::class); }
    public function spaces() { return $this->hasMany(Space::class, 'created_by'); }
    public function tours() { return $this->hasMany(Tour::class, 'created_by'); }
    public function favorites() { return $this->hasMany(Favorite::class); }
    public function feedback() { return $this->hasMany(Feedback::class); }
    public function tourProgresses() { return $this->hasMany(TourProgress::class); }
}
