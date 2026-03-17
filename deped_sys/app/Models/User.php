<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'permissions', // Must be here to save the checklist
        'requires_password_change'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array', 
        ];
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($slug)
    {
        return $this->role && $this->role->slug === $slug;
    }
    
    // STRICT CHECKLIST ENFORCEMENT
    public function hasPermission($permission)
    {
        // SAFETY NET: Ensure the original creator account (User ID 1) NEVER gets locked out.
        // Because your account was made before the checklist existed, your checklist is currently empty!
        if ($this->id === 1) {
            return true;
        }

        // For all other users, strictly check if the box was ticked
        $userPermissions = $this->permissions ?? []; // Default to empty array if null
        
        return is_array($userPermissions) && in_array($permission, $userPermissions);
    }
}