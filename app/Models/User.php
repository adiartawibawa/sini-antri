<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'loket_name', 'is_active', 'is_operator',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_operator' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function queues(): HasMany
    {
        return $this->hasMany(Antrian::class, 'operator_id');
    }

    public function activeQueue(): HasOne
    {
        return $this->hasOne(Antrian::class, 'operator_id')
            ->whereIn('status', ['called', 'serving']);
    }
}
