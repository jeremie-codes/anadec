<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Agent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'matricule',
        'nom',
        'prenoms',
        'date_naissance',
        'lieu_naissance',
        'sexe',
        'situation_matrimoniale',
        'direction',
        'service',
        'role_id',
        'date_recrutement',
        'telephone',
        'email',
        'photo',
        'adresse',
        'statut',
        'date_retraite',
        'date_maladie',
        'date_demission',
        'date_revocation',
        'date_disponibilite',
        'date_detachement',
        'date_mutation',
        'date_reintegration',
        'date_mission',
        'date_deces',
        'motif_changement_statut',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_recrutement' => 'date',
        'date_retraite' => 'date',
        'date_maladie' => 'date',
        'date_demission' => 'date',
        'date_revocation' => 'date',
        'date_disponibilite' => 'date',
        'date_detachement' => 'date',
        'date_mutation' => 'date',
        'date_reintegration' => 'date',
        'date_mission' => 'date',
        'date_deces' => 'date',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

     public function chauffeur()
    {
        return $this->hasOne(Chauffeur::class);
    }

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    // Accesseurs
    public function getFullNameAttribute()
    {
        return $this->nom . ' ' . $this->prenoms;
    }

    public function getInitialsAttribute()
    {
        return strtoupper(substr($this->prenoms, 0, 1) . substr($this->nom, 0, 1));
    }

    public function getAgeAttribute()
    {
        return $this->date_naissance ? $this->date_naissance->age : null;
    }

    public function getAncienneteAttribute()
    {
        return $this->date_recrutement ? $this->date_recrutement->diffInYears(Carbon::now()) : null;
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

    // Scopes
    public function scopeActifs($query)
    {
        return $query->where('statut', 'actif');
    }

    public function scopeByDirection($query, $direction)
    {
        return $query->where('direction', $direction);
    }

    // Méthodes utilitaires
    public function getStatutBadgeClass()
    {
        return match($this->statut) {
            'actif' => 'bg-green-100 text-green-800',
            'retraite' => 'bg-blue-100 text-blue-800',
            'malade' => 'bg-yellow-100 text-yellow-800',
            'demission' => 'bg-gray-100 text-gray-800',
            'revocation' => 'bg-red-100 text-red-800',
            'disponibilite' => 'bg-purple-100 text-purple-800',
            'detachement' => 'bg-indigo-100 text-indigo-800',
            'mutation' => 'bg-pink-100 text-pink-800',
            'reintegration' => 'bg-green-100 text-green-800',
            'mission' => 'bg-orange-100 text-orange-800',
            'deces' => 'bg-black text-white',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatutLabel()
    {
        return match($this->statut) {
            'actif' => 'Actif',
            'retraite' => 'Retraité',
            'malade' => 'Malade',
            'demission' => 'Démission',
            'revocation' => 'Révocation',
            'disponibilite' => 'Disponibilité',
            'detachement' => 'Détachement',
            'mutation' => 'Mutation',
            'reintegration' => 'Réintégration',
            'mission' => 'Mission',
            'deces' => 'Décès',
            default => 'Inconnu',
        };
    }
}
