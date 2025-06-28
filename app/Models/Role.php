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
    public function agents()
    {
        return $this->hasMany(Agent::class);
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

            // Congés
            'conges.view' => 'Voir les congés',
            'conges.dashboard' => 'Dashboard congés',
            'conges.create' => 'Créer un congé',
            'conges.edit' => 'Modifier un congé',
            'conges.delete' => 'Supprimer un congé',
            'conges.approval.directeur' => 'Approuver en tant que directeur',
            'conges.validation.drh' => 'Valider en tant que DRH',
            'conges.mes-conges' => 'Voir mes congés',

            // Cotations
            'cotations.view' => 'Voir les cotations',
            'cotations.dashboard' => 'Dashboard cotations',
            'cotations.create' => 'Créer une cotation',
            'cotations.edit' => 'Modifier une cotation',
            'cotations.delete' => 'Supprimer une cotation',
            'cotations.generate' => 'Générer automatiquement',

            // Rôles et Permissions
            'roles.view' => 'Voir les rôles',
            'roles.edit' => 'Modifier les rôles',
            'roles.permissions' => 'Gérer les permissions',

            // Stock
            'stocks.view' => 'Voir les stocks',
            'stocks.dashboard' => 'Dashboard stock',
            'stocks.create' => 'Créer un stock',
            'stocks.edit' => 'Modifier un stock',
            'stocks.delete' => 'Supprimer un stock',
            'stocks.ajouter' => 'Ajouter au stock',
            'stocks.retirer' => 'Retirer du stock',
            'stocks.mouvements' => 'Voir les mouvements de stock',

            // Demandes de fournitures
            'demandes-fournitures.view' => 'Voir les demandes de fournitures',
            'demandes-fournitures.dashboard' => 'Dashboard fournitures',
            'demandes-fournitures.create' => 'Créer une demande',
            'demandes-fournitures.edit' => 'Modifier une demande',
            'demandes-fournitures.delete' => 'Supprimer une demande',
            'demandes-fournitures.approver' => 'Approuver une demande',
            'demandes-fournitures.livrer' => 'Livrer une demande',
            'demandes-fournitures.mes-demandes' => 'Mes demandes',

            // Véhicules
            'vehicules.view' => 'Voir les véhicules',
            'vehicules.dashboard' => 'Dashboard véhicules',
            'vehicules.create' => 'Créer un véhicule',
            'vehicules.edit' => 'Modifier un véhicule',
            'vehicules.delete' => 'Supprimer un véhicule',
            'vehicules.maintenance' => 'Voir/ajouter une maintenance',

            // Chauffeurs
            'chauffeurs.view' => 'Voir les chauffeurs',
            'chauffeurs.create' => 'Créer un chauffeur',
            'chauffeurs.edit' => 'Modifier un chauffeur',
            'chauffeurs.delete' => 'Supprimer un chauffeur',

            // Demandes de véhicules
            'demandes-vehicules.view' => 'Voir les demandes de véhicules',
            'demandes-vehicules.dashboard' => 'Dashboard demandes véhicules',
            'demandes-vehicules.create' => 'Créer une demande véhicule',
            'demandes-vehicules.edit' => 'Modifier une demande véhicule',
            'demandes-vehicules.delete' => 'Supprimer une demande véhicule',
            'demandes-vehicules.approver' => 'Approuver une demande véhicule',
            'demandes-vehicules.affecter' => 'Affecter un véhicule',
            'demandes-vehicules.mes-demandes' => 'Mes demandes véhicules',

            // Paiements
            'paiements.view' => 'Voir les paiements',
            'paiements.dashboard' => 'Dashboard paiements',
            'paiements.create' => 'Créer un paiement',
            'paiements.edit' => 'Modifier un paiement',
            'paiements.delete' => 'Supprimer un paiement',
            'paiements.valider' => 'Valider un paiement',
            'paiements.payer' => 'Effectuer un paiement',
            'paiements.fiches-paie' => 'Voir les fiches de paie',
            'paiements.mes-paiements' => 'Mes paiements',

            // Courriers
            'courriers.view' => 'Voir les courriers',
            'courriers.dashboard' => 'Dashboard courriers',
            'courriers.create' => 'Créer un courrier',
            'courriers.edit' => 'Modifier un courrier',
            'courriers.delete' => 'Supprimer un courrier',
            'courriers.traiter' => 'Traiter un courrier',
            'courriers.archiver' => 'Archiver un courrier',
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

            // Valves
            'valves.view' => 'Voir les valves',
            'valves.dashboard' => 'Dashboard valves',
            'valves.create' => 'Créer un communiqué',
            'valves.edit' => 'Modifier un communiqué',
            'valves.delete' => 'Supprimer un communiqué',
        ];
    }

    // Rôles prédéfinis
    public static function getDefaultRoles()
    {
        return [
            'directeur' => [
                'name' => 'directeur',
                'display_name' => 'Directeur',
                'description' => 'Directeur avec pleins pouvoirs',
                'permissions' => [
                    'dashboard.view',
                    'agents.view', 'agents.create', 'agents.edit', 'agents.delete', 'agents.export',
                    'presences.view', 'presences.create', 'presences.edit', 'presences.delete', 'presences.export',
                    'conges.view', 'conges.dashboard', 'conges.create', 'conges.edit', 'conges.delete', 'conges.approval.directeur',
                    'cotations.view', 'cotations.dashboard', 'cotations.create', 'cotations.edit', 'cotations.delete', 'cotations.generate',
                    'roles.view', 'roles.edit', 'roles.permissions',
                    'stocks.view', 'stocks.dashboard', 'stocks.create', 'stocks.edit', 'stocks.delete',
                    'demandes-fournitures.view', 'demandes-fournitures.dashboard', 'demandes-fournitures.approver',
                    'vehicules.view', 'vehicules.dashboard', 'vehicules.create', 'vehicules.edit', 'vehicules.delete',
                    'chauffeurs.view', 'chauffeurs.create', 'chauffeurs.edit', 'chauffeurs.delete',
                    'demandes-vehicules.view', 'demandes-vehicules.dashboard', 'demandes-vehicules.approver', 'demandes-vehicules.affecter',
                    'paiements.view', 'paiements.dashboard', 'paiements.create', 'paiements.edit', 'paiements.valider', 'paiements.payer',
                    'courriers.view', 'courriers.dashboard', 'courriers.create', 'courriers.edit', 'courriers.traiter', 'courriers.archiver',
                    'visitors.view', 'visitors.create', 'visitors.edit', 'visitors.delete',
                    'valves.view', 'valves.dashboard', 'valves.create', 'valves.edit', 'valves.delete',
                ],
            ],
            'sous-directeur' => [
                'name' => 'sous-directeur',
                'display_name' => 'Sous-Directeur',
                'description' => 'Sous-directeur avec permissions étendues',
                'permissions' => [
                    'dashboard.view',
                    'agents.view', 'agents.create', 'agents.edit',
                    'presences.view', 'presences.create', 'presences.edit',
                    'conges.view', 'conges.dashboard', 'conges.create', 'conges.edit', 'conges.approval.directeur',
                    'cotations.view', 'cotations.dashboard', 'cotations.create', 'cotations.edit',
                    'stocks.view', 'stocks.dashboard',
                    'demandes-fournitures.view', 'demandes-fournitures.dashboard', 'demandes-fournitures.approver',
                    'vehicules.view', 'vehicules.dashboard',
                    'demandes-vehicules.view', 'demandes-vehicules.dashboard', 'demandes-vehicules.approver',
                    'courriers.view', 'courriers.dashboard', 'courriers.create', 'courriers.traiter',
                    'visitors.view', 'visitors.create', 'visitors.edit',
                    'valves.view', 'valves.create', 'valves.edit',
                ],
            ],
            'assistant' => [
                'name' => 'assistant',
                'display_name' => 'Assistant',
                'description' => 'Assistant avec permissions limitées',
                'permissions' => [
                    'dashboard.view',
                    'agents.view',
                    'presences.view', 'presences.create',
                    'conges.view', 'conges.create',
                    'courriers.view', 'courriers.create',
                    'visitors.view', 'visitors.create',
                    'valves.view',
                ],
            ],
            'secretaire' => [
                'name' => 'secretaire',
                'display_name' => 'Secrétaire',
                'description' => 'Secrétaire avec accès au courrier et visiteurs',
                'permissions' => [
                    'dashboard.view',
                    'courriers.view', 'courriers.create', 'courriers.edit', 'courriers.traiter',
                    'visitors.view', 'visitors.create', 'visitors.edit',
                    'valves.view', 'valves.create',
                ],
            ],
            'chef-service' => [
                'name' => 'chef-service',
                'display_name' => 'Chef de Service',
                'description' => 'Chef de service avec permissions de supervision',
                'permissions' => [
                    'dashboard.view',
                    'agents.view',
                    'presences.view', 'presences.create', 'presences.edit',
                    'conges.view', 'conges.create', 'conges.edit',
                    'cotations.view', 'cotations.create',
                    'demandes-fournitures.view', 'demandes-fournitures.create',
                    'demandes-vehicules.view', 'demandes-vehicules.create',
                    'visitors.view', 'visitors.create',
                ],
            ],
            'chef-s-principal' => [
                'name' => 'chef-s-principal',
                'display_name' => 'Chef de Service Principal',
                'description' => 'Chef de service principal avec permissions étendues',
                'permissions' => [
                    'dashboard.view',
                    'agents.view', 'agents.edit',
                    'presences.view', 'presences.create', 'presences.edit',
                    'conges.view', 'conges.create', 'conges.edit',
                    'cotations.view', 'cotations.create', 'cotations.edit',
                    'demandes-fournitures.view', 'demandes-fournitures.create', 'demandes-fournitures.approver',
                    'demandes-vehicules.view', 'demandes-vehicules.create', 'demandes-vehicules.approver',
                    'courriers.view', 'courriers.create', 'courriers.traiter',
                    'visitors.view', 'visitors.create', 'visitors.edit',
                    'valves.view', 'valves.create',
                ],
            ],
            'collaborateur' => [
                'name' => 'collaborateur',
                'display_name' => 'Collaborateur',
                'description' => 'Collaborateur avec accès de base',
                'permissions' => [
                    'dashboard.view',
                    'agents.view',
                    'presences.view',
                    'conges.view', 'conges.create', 'conges.mes-conges',
                    'demandes-fournitures.view', 'demandes-fournitures.create', 'demandes-fournitures.mes-demandes',
                    'demandes-vehicules.view', 'demandes-vehicules.create', 'demandes-vehicules.mes-demandes',
                    'paiements.mes-paiements',
                    'valves.view',
                ],
            ],
            'maitrise' => [
                'name' => 'maitrise',
                'display_name' => 'Maîtrise',
                'description' => 'Agent de maîtrise avec accès intermédiaire',
                'permissions' => [
                    'dashboard.view',
                    'agents.view',
                    'presences.view', 'presences.create',
                    'conges.view', 'conges.create', 'conges.mes-conges',
                    'stocks.view',
                    'demandes-fournitures.view', 'demandes-fournitures.create', 'demandes-fournitures.mes-demandes',
                    'vehicules.view',
                    'demandes-vehicules.view', 'demandes-vehicules.create', 'demandes-vehicules.mes-demandes',
                    'paiements.mes-paiements',
                    'visitors.view', 'visitors.create',
                    'valves.view',
                ],
            ],
            'execution' => [
                'name' => 'execution',
                'display_name' => 'Exécution',
                'description' => 'Agent d\'exécution avec accès minimal',
                'permissions' => [
                    'dashboard.view',
                    'conges.mes-conges',
                    'demandes-fournitures.mes-demandes',
                    'demandes-vehicules.mes-demandes',
                    'paiements.mes-paiements',
                    'valves.view',
                ],
            ],
        ];
    }

    public function getBadgeClass()
    {
        return match($this->name) {
            'directeur' => 'bg-red-100 text-red-800',
            'sous-directeur' => 'bg-purple-100 text-purple-800',
            'assistant' => 'bg-indigo-100 text-indigo-800',
            'secretaire' => 'bg-blue-100 text-blue-800',
            'chef-service' => 'bg-green-100 text-green-800',
            'chef-s-principal' => 'bg-emerald-100 text-emerald-800',
            'collaborateur' => 'bg-yellow-100 text-yellow-800',
            'maitrise' => 'bg-orange-100 text-orange-800',
            'execution' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getIcon()
    {
        return match($this->name) {
            'directeur' => 'bx-crown',
            'sous-directeur' => 'bx-shield',
            'assistant' => 'bx-user-voice',
            'secretaire' => 'bx-edit',
            'chef-service' => 'bx-user-check',
            'chef-s-principal' => 'bx-user-plus',
            'collaborateur' => 'bx-group',
            'maitrise' => 'bx-wrench',
            'execution' => 'bx-user',
            default => 'bx-user',
        ];
    }
}