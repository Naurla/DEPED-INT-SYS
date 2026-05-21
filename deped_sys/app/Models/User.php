<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, LogsActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'requires_password_change',
        'profile_photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            
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
    
    // STRICT CHECKLIST ENFORCEMENT (Now checking the Role!)
    public function hasPermission($permission)
    {
        // SAFETY NET: Ensure the original creator account (User ID 1) NEVER gets locked out.
        if ($this->id === 1) {
            return true;
        }

        // Super admins automatically bypass all restrictions
        if ($this->role && $this->role->slug === 'super-admin') {
            return true;
        }

        // Fetch the permissions array from the user's assigned role
        $rolePermissions = $this->role ? ($this->role->permissions ?? []) : [];

        // Safety fallback: decode if it's returning as a JSON string
        if (is_string($rolePermissions)) {
            $rolePermissions = json_decode($rolePermissions, true) ?? [];
        }
        
        // Strictly check if the box was ticked on the Role
        return is_array($rolePermissions) && in_array($permission, $rolePermissions);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty() 
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "User has been {$eventName}");
    }
}