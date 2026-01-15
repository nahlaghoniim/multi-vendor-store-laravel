<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'super_admin',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'super_admin' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * Automatically hash passwords when setting
     */
    public function setPasswordAttribute($password)
    {
        if ($password) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

    /**
     * Roles relationship (many-to-many polymorphic)
     */
    public function roles()
    {
        return $this->morphToMany(Role::class, 'authorizable', 'role_user');
    }

    /**
     * Check if admin has a specific role
     */
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Check if admin is a super admin
     */
    public function isSuperAdmin()
    {
        return $this->super_admin;
    }

    public function profile()
{
    return $this->hasOne(Profile::class, 'admin_id');
}
 public function routeNotificationForBroadcast($notification)
    {
        return 'App.Models.Admin.' . $this->id;
    }
}
