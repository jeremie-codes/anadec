<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    // Relations
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Méthodes utilitaires
    public function hasPermission($permission)
    {
        return in_array($permission, $this->permissions ?? []);
    }

    public function grantPermission($permission)
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->permissions = $permissions;
            $this->save();
        }
    }

    public function revokePermission($permission)
    {
        $permissions = $this->permissions ?? [];
        $this->permissions = array_values(array_diff($permissions, [$permission]));
        $this->save();
    }

    public function syncPermissions(array $permissions)
    {
        $this->permissions = $permissions;
        $this->save();
    }

    // Permissions disponibles dans le système
    public static function getAvailablePermissions()
    {
        return [
            // Dashboard
            'dashboard.view' => 'Accéder au tableau de bord',

            // Profil utilisateur
            'profile.view' => 'Voir son profil',
            'profile.edit' => 'Modifier son profil',
            'profile.update' => 'Mettre à jour son profil',
            'profile.delete-photo' => 'Supprimer la photo de profil',

            // Agents
            'agents.view' => 'Voir les agents',
            'agents.create' => 'Créer un agent',
            'agents.edit' => 'Modifier un agent',
            'agents.delete' => 'Supprimer un agent',
            'agents.export' => 'Exporter les agents',
            'agents.identification' => 'Liste d\'identification',
            'agents.retraites' => 'Liste des retraités',
            'agents.malades' => 'Liste des malades',
            'agents.demissions' => 'Liste des démissions',
            'agents.revocations' => 'Liste des révocations',
            'agents.disponibilites' => 'Liste des disponibilités',
            'agents.detachements' => 'Liste des détachements',
            'agents.mutations' => 'Liste des mutations',
            'agents.reintegrations' => 'Liste des réintégrations',
            'agents.missions' => 'Liste des missions',
            'agents.deces' => 'Liste des décès',

            // Présences
            'presences.view' => 'Voir les présences',
            'presences.daily' => 'Voir la présence quotidienne',
            'presences.create' => 'Créer une présence',
            'presences.edit' => 'Modifier une présence',
            'presences.delete' => 'Supprimer une présence',
            'presences.export' => 'Exporter les présences',
            'presences.filter' => 'Filtrer les présences',

            // Congés
            'conges.view' => 'Voir les congés',
            'conges.dashboard' => 'Dashboard congés',
            'conges.create' => 'Créer un congé',
            'conges.edit' => 'Modifier un congé',
            'conges.delete' => 'Supprimer un congé',
            'conges.show' => 'Détails d\'un congé',
            'conges.approval.directeur' => 'Approuver en tant que directeur',
            'conges.validation.drh' => 'Valider en tant que DRH',
            'conges.mes-conges' => 'Voir mes congés',
            'conges.solde' => 'Calculer le solde des congés',

            // Cotations
            'cotations.view' => 'Voir les cotations',
            'cotations.dashboard' => 'Dashboard cotations',
            'cotations.create' => 'Créer une cotation',
            'cotations.edit' => 'Modifier une cotation',
            'cotations.delete' => 'Supprimer une cotation',
            'cotations.show' => 'Détails d\'une cotation',
            'cotations.generate' => 'Générer automatiquement',

            // Rôles et Permissions
            'roles.view' => 'Voir les rôles',
            'roles.edit' => 'Modifier les rôles',
            'roles.users' => 'Voir les utilisateurs par rôle',
            'roles.permissions.matrix' => 'Voir la matrice des permissions',
            'roles.permissions.update' => 'Mettre à jour les permissions',

            // Utilisateurs
            'users.view' => 'Voir les utilisateurs',
            'users.create' => 'Créer un utilisateur',
            'users.edit' => 'Modifier un utilisateur',
            'users.delete' => 'Supprimer un utilisateur',
            'users.update-role' => 'Changer le rôle d\'un utilisateur',

            // Stock
            'stocks.view' => 'Voir les stocks',
            'stocks.dashboard' => 'Dashboard stock',
            'stocks.create' => 'Créer un stock',
            'stocks.edit' => 'Modifier un stock',
            'stocks.delete' => 'Supprimer un stock',
            'stocks.show' => 'Détails du stock',
            'stocks.ajouter' => 'Ajouter au stock',
            'stocks.retirer' => 'Retirer du stock',
            'stocks.mouvements' => 'Voir les mouvements de stock',

            // Demandes de fournitures
            'demandes-fournitures.view' => 'Voir les demandes de fournitures',
            'demandes-fournitures.dashboard' => 'Dashboard fournitures',
            'demandes-fournitures.create' => 'Créer une demande',
            'demandes-fournitures.edit' => 'Modifier une demande',
            'demandes-fournitures.delete' => 'Supprimer une demande',
            'demandes-fournitures.show' => 'Détails d\'une demande',
            'demandes-fournitures.approver' => 'Approuver une demande',
            'demandes-fournitures.livrer' => 'Livrer une demande',
            'demandes-fournitures.mes-demandes' => 'Mes demandes',

            // Véhicules
            'vehicules.view' => 'Voir les véhicules',
            'vehicules.dashboard' => 'Dashboard véhicules',
            'vehicules.create' => 'Créer un véhicule',
            'vehicules.edit' => 'Modifier un véhicule',
            'vehicules.delete' => 'Supprimer un véhicule',
            'vehicules.show' => 'Détails d\'un véhicule',
            'vehicules.maintenance' => 'Voir/ajouter une maintenance',
            'vehicules.statut' => 'Changer statut du véhicule',

            // Chauffeurs
            'chauffeurs.view' => 'Voir les chauffeurs',
            'chauffeurs.create' => 'Créer un chauffeur',
            'chauffeurs.edit' => 'Modifier un chauffeur',
            'chauffeurs.delete' => 'Supprimer un chauffeur',
            'chauffeurs.show' => 'Détails d\'un chauffeur',
            'chauffeurs.disponibles' => 'Voir les chauffeurs disponibles',

            // Demandes de véhicules
            'demandes-vehicules.view' => 'Voir les demandes de véhicules',
            'demandes-vehicules.dashboard' => 'Dashboard demandes véhicules',
            'demandes-vehicules.create' => 'Créer une demande véhicule',
            'demandes-vehicules.edit' => 'Modifier une demande véhicule',
            'demandes-vehicules.delete' => 'Supprimer une demande véhicule',
            'demandes-vehicules.show' => 'Détails d\'une demande véhicule',
            'demandes-vehicules.approver' => 'Approuver une demande véhicule',
            'demandes-vehicules.affecter' => 'Affecter un véhicule',
            'demandes-vehicules.demarrer' => 'Démarrer une mission',
            'demandes-vehicules.terminer' => 'Terminer une mission',
            'demandes-vehicules.mes-demandes' => 'Mes demandes véhicules',

            // Paiements
            'paiements.view' => 'Voir les paiements',
            'paiements.dashboard' => 'Dashboard paiements',
            'paiements.create' => 'Créer un paiement',
            'paiements.edit' => 'Modifier un paiement',
            'paiements.delete' => 'Supprimer un paiement',
            'paiements.show' => 'Détails d\'un paiement',
            'paiements.valider' => 'Valider un paiement',
            'paiements.payer' => 'Effectuer un paiement',
            'paiements.fiches-paie' => 'Voir les fiches de paie',
            'paiements.fiche-paie' => 'Détail fiche de paie',
            'paiements.mes-paiements' => 'Mes paiements',
            'paiements.calcul' => 'Calculer salaire ou décompte',

            // Courriers
            'courriers.view' => 'Voir les courriers',
            'courriers.dashboard' => 'Dashboard courriers',
            'courriers.create' => 'Créer un courrier',
            'courriers.edit' => 'Modifier un courrier',
            'courriers.delete' => 'Supprimer un courrier',
            'courriers.show' => 'Détails courrier',
            'courriers.traiter' => 'Traiter un courrier',
            'courriers.archiver' => 'Archiver un courrier',
            'courriers.documents' => 'Gérer les documents du courrier',
            'courriers.entrants' => 'Voir courriers entrants',
            'courriers.sortants' => 'Voir courriers sortants',
            'courriers.internes' => 'Voir courriers internes',
            'courriers.non-traites' => 'Voir courriers non traités',
            'courriers.archives' => 'Voir archives de courriers',

            // Visiteurs
            'visitors.view' => 'Voir les visiteurs',
            'visitors.create' => 'Ajouter un visiteur',
            'visitors.edit' => 'Modifier un visiteur',
            'visitors.delete' => 'Supprimer un visiteur',
            'visitors.show' => 'Détails d\'un visiteur',
            'visitors.marquer-sortie' => 'Marquer la sortie',

            // Valves
            'valves.view' => 'Voir les valves',
            'valves.dashboard' => 'Dashboard valves',
            'valves.create' => 'Créer un communiqué',
            'valves.edit' => 'Modifier un communiqué',
            'valves.delete' => 'Supprimer un communiqué',
            'valves.show' => 'Détails du communiqué',
            'valves.toggle-actif' => 'Activer/Désactiver un communiqué',
        ];
    }

    // Rôles prédéfinis avec leurs permissions
    public static function getDefaultRoles()
    {
        return [
            'agent' => [
                'name' => 'agent',
                'display_name' => 'Agent',
                'description' => 'Agent de base avec accès limité',
                'permissions' => [
                    'dashboard.view',
                    'conges.view_own',
                    'conges.create',
                    'presences.view',
                    'demandes-fournitures.mes-demandes',
                    'demandes-vehicules.mes-demandes',
                    'paiements.mes-paiements',
                    'valves.view',
                ],
            ],
            'responsable_service' => [
                'name' => 'responsable_service',
                'display_name' => 'Responsable de Service',
                'description' => 'Responsable d\'un service avec permissions étendues',
                'permissions' => [
                    'dashboard.view',
                    'agents.view',
                    'presences.view',
                    'presences.create',
                    'presences.edit',
                    'conges.view',
                    'conges.create',
                    'conges.edit',
                    'cotations.view',
                    'reports.view',
                    'demandes-fournitures.view',
                    'demandes-fournitures.create',
                    'demandes-vehicules.view',
                    'demandes-vehicules.create',
                    'visitors.view',
                    'visitors.create',
                    'valves.view',
                ],
            ],
            'sous_directeur' => [
                'name' => 'sous_directeur',
                'display_name' => 'Sous-Directeur',
                'description' => 'Sous-directeur avec permissions de supervision',
                'permissions' => [
                    'dashboard.view',
                    'agents.view',
                    'agents.edit',
                    'presences.view',
                    'presences.create',
                    'presences.edit',
                    'conges.view',
                    'conges.create',
                    'conges.edit',
                    'conges.approve_directeur',
                    'cotations.view',
                    'cotations.create',
                    'reports.view',
                    'reports.export',
                    'demandes-fournitures.view',
                    'demandes-fournitures.create',
                    'demandes-fournitures.approbation',
                    'demandes-vehicules.view',
                    'demandes-vehicules.create',
                    'demandes-vehicules.approbation',
                    'visitors.view',
                    'visitors.create',
                    'visitors.edit',
                    'valves.view',
                    'valves.create',
                    'valves.edit',
                ],
            ],
            'directeur' => [
                'name' => 'directeur',
                'display_name' => 'Directeur',
                'description' => 'Directeur avec pleins pouvoirs sur son département',
                'permissions' => [
                    'dashboard.view',
                    'agents.view',
                    'agents.create',
                    'agents.edit',
                    'agents.delete',
                    'presences.view',
                    'presences.create',
                    'presences.edit',
                    'presences.delete',
                    'conges.view_all',
                    'conges.create',
                    'conges.edit',
                    'conges.delete',
                    'conges.approve_directeur',
                    'cotations.view',
                    'cotations.create',
                    'cotations.edit',
                    'cotations.generate',
                    'reports.view',
                    'reports.export',
                    'users.view',
                    'demandes-fournitures.view',
                    'demandes-fournitures.create',
                    'demandes-fournitures.edit',
                    'demandes-fournitures.delete',
                    'demandes-fournitures.approbation',
                    'demandes-vehicules.view',
                    'demandes-vehicules.create',
                    'demandes-vehicules.edit',
                    'demandes-vehicules.delete',
                    'demandes-vehicules.approbation',
                    'visitors.view',
                    'visitors.create',
                    'visitors.edit',
                    'visitors.delete',
                    'valves.view',
                    'valves.create',
                    'valves.edit',
                    'valves.delete',
                ],
            ],
            'rh' => [
                'name' => 'rh',
                'display_name' => 'RH',
                'description' => 'Agent RH avec accès aux fonctions RH',
                'permissions' => [
                    'dashboard.view',
                    'agents.view',
                    'agents.create',
                    'agents.edit',
                    'presences.view',
                    'presences.create',
                    'presences.edit',
                    'conges.view_all',
                    'conges.create',
                    'conges.edit',
                    'cotations.view',
                    'cotations.create',
                    'cotations.edit',
                    'reports.view',
                    'reports.export',
                    'users.view',
                    'users.create',
                    'users.edit',
                    'paiements.view',
                    'paiements.create',
                    'paiements.validation',
                    'visitors.view',
                    'visitors.create',
                    'visitors.edit',
                    'valves.view',
                    'valves.create',
                    'valves.edit',
                ],
            ],
            'drh' => [
                'name' => 'drh',
                'display_name' => 'DRH',
                'description' => 'Directeur des Ressources Humaines - Accès complet',
                'permissions' => [
                    'dashboard.view',
                    'agents.view',
                    'agents.create',
                    'agents.edit',
                    'agents.delete',
                    'agents.export',
                    'presences.view',
                    'presences.create',
                    'presences.edit',
                    'presences.delete',
                    'presences.export',
                    'conges.view_all',
                    'conges.create',
                    'conges.edit',
                    'conges.delete',
                    'conges.approve_directeur',
                    'conges.validate_drh',
                    'cotations.view',
                    'cotations.create',
                    'cotations.edit',
                    'cotations.delete',
                    'cotations.generate',
                    'reports.view',
                    'reports.export',
                    'users.view',
                    'users.create',
                    'users.edit',
                    'users.delete',
                    'roles.view',
                    'roles.edit',
                    'system.settings',
                    'system.backup',
                    'system.logs',
                    'stocks.view',
                    'stocks.create',
                    'stocks.edit',
                    'stocks.delete',
                    'demandes-fournitures.view',
                    'demandes-fournitures.create',
                    'demandes-fournitures.edit',
                    'demandes-fournitures.delete',
                    'demandes-fournitures.approbation',
                    'vehicules.view',
                    'vehicules.create',
                    'vehicules.edit',
                    'vehicules.delete',
                    'chauffeurs.view',
                    'chauffeurs.create',
                    'chauffeurs.edit',
                    'chauffeurs.delete',
                    'demandes-vehicules.view',
                    'demandes-vehicules.create',
                    'demandes-vehicules.edit',
                    'demandes-vehicules.delete',
                    'demandes-vehicules.approbation',
                    'demandes-vehicules.affectation',
                    'paiements.view',
                    'paiements.create',
                    'paiements.edit',
                    'paiements.delete',
                    'paiements.validation',
                    'paiements.paiement',
                    'courriers.view',
                    'courriers.create',
                    'courriers.edit',
                    'courriers.delete',
                    'courriers.traiter',
                    'courriers.archiver',
                    'visitors.view',
                    'visitors.create',
                    'visitors.edit',
                    'visitors.delete',
                    'valves.view',
                    'valves.create',
                    'valves.edit',
                    'valves.delete',
                ],
            ],
            'logistique' => [
                'name' => 'logistique',
                'display_name' => 'Logistique',
                'description' => 'Responsable logistique avec accès aux stocks et véhicules',
                'permissions' => [
                    'dashboard.view',
                    'stocks.view',
                    'stocks.create',
                    'stocks.edit',
                    'stocks.delete',
                    'demandes-fournitures.view',
                    'demandes-fournitures.create',
                    'demandes-fournitures.edit',
                    'demandes-fournitures.approbation',
                    'vehicules.view',
                    'vehicules.create',
                    'vehicules.edit',
                    'chauffeurs.view',
                    'chauffeurs.create',
                    'chauffeurs.edit',
                    'demandes-vehicules.view',
                    'demandes-vehicules.create',
                    'demandes-vehicules.approbation',
                    'demandes-vehicules.affectation',
                ],
            ],
            'finance' => [
                'name' => 'finance',
                'display_name' => 'Finance',
                'description' => 'Responsable financier avec accès aux paiements',
                'permissions' => [
                    'dashboard.view',
                    'agents.view',
                    'paiements.view',
                    'paiements.create',
                    'paiements.edit',
                    'paiements.validation',
                    'paiements.paiement',
                    'reports.view',
                    'reports.export',
                ],
            ],
            'secretariat' => [
                'name' => 'secretariat',
                'display_name' => 'Secrétariat',
                'description' => 'Secrétaire avec accès au courrier et visiteurs',
                'permissions' => [
                    'dashboard.view',
                    'courriers.view',
                    'courriers.create',
                    'courriers.edit',
                    'courriers.traiter',
                    'courriers.archiver',
                    'visitors.view',
                    'visitors.create',
                    'visitors.edit',
                    'valves.view',
                    'valves.create',
                ],
            ],
        ];
    }


    public function getBadgeClass()
    {
        return match($this->name) {
            'agent' => 'bg-gray-100 text-gray-800',
            'responsable_service' => 'bg-blue-100 text-blue-800',
            'sous_directeur' => 'bg-indigo-100 text-indigo-800',
            'directeur' => 'bg-purple-100 text-purple-800',
            'rh' => 'bg-green-100 text-green-800',
            'drh' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getIcon()
    {
        return match($this->name) {
            'agent' => 'bx-user',
            'responsable_service' => 'bx-user-check',
            'sous_directeur' => 'bx-user-voice',
            'directeur' => 'bx-shield',
            'rh' => 'bx-group',
            'drh' => 'bx-crown',
            default => 'bx-user',
        };
    }
}
