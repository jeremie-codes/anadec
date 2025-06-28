<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'photo',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relations
    public function agent()
    {
        return $this->hasOne(Agent::class);
    }

    // Méthodes utilitaires pour la photo
    public function getInitialsAttribute()
    {
        $names = explode(' ', $this->name);
        $initials = '';

        foreach ($names as $name) {
            $initials .= strtoupper(substr($name, 0, 1));
        }

        return substr($initials, 0, 2); // Maximum 2 initiales
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo && file_exists(public_path('storage/' . $this->photo))) {
            return asset('storage/' . $this->photo);
        }

        return null;
    }

    public function hasPhoto()
    {
        return $this->photo && file_exists(public_path('storage/' . $this->photo));
    }

    // Méthodes de permissions
    public function hasPermission($permission)
    {
        return $this->agent->role && $this->agent->role->hasPermission($permission);
    }

    public function hasRole($roleName)
    {
        return $this->agent->role && $this->agent->role->name === $roleName;
    }

    public function hasAnyRole(array $roles)
    {
        return $this->agent->role && in_array($this->agent->role->name, $roles);
    }

    // Vérifier si l'utilisateur peut accéder à une ressource
    public function canAccess($resource, $action = 'view')
    {
        $permission = "{$resource}.{$action}";
        return $this->hasPermission($permission);
    }

    // Vérifier si l'utilisateur est un administrateur
    public function isAdmin()
    {
        return $this->hasRole('drh');
    }

    // Vérifier si l'utilisateur est un directeur
    public function isDirecteur()
    {
        return $this->hasAnyRole(['directeur', 'sous_directeur', 'drh']);
    }

    // Vérifier si l'utilisateur est du personnel RH
    public function isRH()
    {
        return $this->hasAnyRole(['rh', 'drh']);
    }
}
