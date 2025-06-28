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

    // Méthodes de permissions basées sur le rôle de l'agent
    public function hasPermission($permission)
    {
        if (!$this->agent || !$this->agent->role) {
            return false;
        }

        return $this->agent->role->hasPermission($permission);
    }

    public function hasRole($roleName)
    {
        if (!$this->agent || !$this->agent->role) {
            return false;
        }

        return $this->agent->role->name === $roleName;
    }

    public function hasAnyRole(array $roles)
    {
        if (!$this->agent || !$this->agent->role) {
            return false;
        }

        return in_array($this->agent->role->name, $roles);
    }

    // Vérifier si l'utilisateur peut accéder à une ressource
    public function canAccess($resource, $action = 'view')
    {
        $permission = "{$resource}.{$action}";
        return $this->hasPermission($permission);
    }

    // Vérifier si l'utilisateur est un directeur
    public function isDirecteur()
    {
        return $this->hasAnyRole(['directeur', 'sous-directeur']);
    }

    // Vérifier si l'utilisateur est un chef
    public function isChef()
    {
        return $this->hasAnyRole(['directeur', 'sous-directeur', 'chef-service', 'chef-s-principal']);
    }

    // Obtenir le rôle de l'utilisateur
    public function getRole()
    {
        return $this->agent?->role;
    }

    // Obtenir le nom du rôle
    public function getRoleName()
    {
        return $this->agent?->role?->name;
    }

    // Obtenir le nom d'affichage du rôle
    public function getRoleDisplayName()
    {
        return $this->agent?->role?->display_name ?? 'Aucun rôle';
    }
}