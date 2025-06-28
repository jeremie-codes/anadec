<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Conge extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'date_debut',
        'date_fin',
        'nombre_jours',
        'motif',
        'justificatif',
        'type',
        'statut',
        'commentaire_directeur',
        'commentaire_drh',
        'date_approbation_directeur',
        'date_validation_drh',
        'approuve_par_directeur',
        'valide_par_drh',
        'traiter_par_rh',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_approbation_directeur' => 'date',
        'date_validation_drh' => 'date',
    ];

    // Relations
    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function approbateurDirecteur()
    {
        return $this->belongsTo(User::class, 'approuve_par_directeur');
    }

    public function validateurDrh()
    {
        return $this->belongsTo(User::class, 'valide_par_drh');
    }

    public function traiterRh()
    {
        return $this->belongsTo(User::class, 'traiter_par_rh');
    }

    // Méthodes utilitaires
    public function getStatutBadgeClass()
    {
        return match($this->statut) {
            'en_attente' => 'bg-yellow-100 text-yellow-800',
            'approuve_directeur' => 'bg-blue-100 text-blue-800',
            'valide_drh' => 'bg-green-100 text-green-800',
            'traiter_rh' => 'bg-yellow-500 text-yellow-500',
            'rejete' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatutLabel()
    {
        return match($this->statut) {
            'en_attente' => 'En attente',
            'approuve_directeur' => 'Approuvé Directeur',
            'valide_drh' => 'Validé DRH',
            'traiter_rh' => 'Traité RH',
            'rejete' => 'Rejeté',
            default => 'Inconnu',
        };
    }

    public function getStatutIcon()
    {
        return match($this->statut) {
            'en_attente' => 'bx-time-five',
            'approuve_directeur' => 'bx-check',
            'valide_drh' => 'bx-check-double',
            'traiter_rh' => 'bx-check',
            'rejete' => 'bx-x',
            default => 'bx-help-circle',
        };
    }

    public function getTypeLabel()
    {
        return match($this->type) {
            'annuel' => 'Congé annuel',
            'maladie' => 'Congé maladie',
            'maternite' => 'Congé maternité',
            'paternite' => 'Congé paternité',
            'exceptionnel' => 'Congé exceptionnel',
            default => 'Autre',
        };
    }

    public function getTypeBadgeClass()
    {
        return match($this->type) {
            'annuel' => 'bg-blue-100 text-blue-800',
            'maladie' => 'bg-red-100 text-red-800',
            'maternite' => 'bg-pink-100 text-pink-800',
            'paternite' => 'bg-green-100 text-green-800',
            'exceptionnel' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getJustificatifUrlAttribute()
    {
        if ($this->justificatif && file_exists(public_path('storage/' . $this->justificatif))) {
            return asset('storage/' . $this->justificatif);
        }
        return null;
    }

    public function hasJustificatif()
    {
        return $this->justificatif && file_exists(public_path('storage/' . $this->justificatif));
    }

    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeApprouveDirecteur($query)
    {
        return $query->where('statut', 'approuve_directeur');
    }

    public function scopeTraiterRh($query)
    {
        return $query->where('statut', 'traiter_rh');
    }

    public function scopeValide($query)
    {
        return $query->where('statut', 'valide_drh');
    }

    public function scopeRejete($query)
    {
        return $query->where('statut', 'rejete');
    }

    public function scopeEnCours($query)
    {
        return $query->where('date_debut', '<=', now())
                    ->where('date_fin', '>=', now())
                    ->where('statut', 'valide_drh');
    }

    // Méthodes de calcul
    public static function calculerNombreJours($dateDebut, $dateFin)
    {
        $debut = Carbon::parse($dateDebut);
        $fin = Carbon::parse($dateFin);

        $jours = 0;
        $current = $debut->copy();

        while ($current->lte($fin)) {
            // Ne compter que les jours ouvrables (lundi à vendredi)
            if ($current->isWeekday()) {
                $jours++;
            }
            $current->addDay();
        }

        return $jours;
    }

    public function estEnCours()
    {
        return $this->statut === 'valide_drh'
            && $this->date_debut <= now()
            && $this->date_fin >= now();
    }

    public function peutEtreModifie()
    {
        return $this->statut === 'approuve_directeur';
    }

    public function peutEtreApprouveParDirecteur()
    {
        return $this->statut === 'en_attente';
    }

    public function peutEtreValideParDrh()
    {
        return $this->statut === 'traiter_rh';
    }
}
