<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\CongeController;
use App\Http\Controllers\CotationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DemandeFournitureController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\ChauffeurController;
use App\Http\Controllers\DemandeVehiculeController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\CourrierController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\ValveController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Routes d'authentification
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});


// Routes protégées
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard principal
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profil utilisateur
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::delete('/delete-photo', [ProfileController::class, 'deletePhoto'])->name('delete-photo');
    });

    // Module Gestion des agents
    Route::prefix('agents')->name('agents.')->group(function () {
        Route::get('/', [AgentController::class, 'index'])->name('index');
        Route::get('/create', [AgentController::class, 'create'])->name('create');
        Route::post('/', [AgentController::class, 'store'])->name('store');
        Route::get('/{agent}', [AgentController::class, 'show'])->name('show');
        Route::get('/{agent}/edit', [AgentController::class, 'edit'])->name('edit');
        Route::put('/{agent}', [AgentController::class, 'update'])->name('update');
        Route::delete('/{agent}', [AgentController::class, 'destroy'])->name('destroy');

        // Sous-modules agents
        Route::get('/identification/list', [AgentController::class, 'identification'])->name('identification');
        Route::get('/retraites/list', [AgentController::class, 'retraites'])->name('retraites');
        Route::get('/malades/list', [AgentController::class, 'malades'])->name('malades');
        Route::get('/demissions/list', [AgentController::class, 'demissions'])->name('demissions');
        Route::get('/revocations/list', [AgentController::class, 'revocations'])->name('revocations');
        Route::get('/disponibilites/list', [AgentController::class, 'disponibilites'])->name('disponibilites');
        Route::get('/detachements/list', [AgentController::class, 'detachements'])->name('detachements');
        Route::get('/mutations/list', [AgentController::class, 'mutations'])->name('mutations');
        Route::get('/reintegrations/list', [AgentController::class, 'reintegrations'])->name('reintegrations');
        Route::get('/missions/list', [AgentController::class, 'missions'])->name('missions');
        Route::get('/deces/list', [AgentController::class, 'deces'])->name('deces');
    });

    // Module Gestion des présences
    Route::prefix('presences')->name('presences.')->group(function () {
        Route::get('/', [PresenceController::class, 'index'])->name('index');
        Route::get('/daily', [PresenceController::class, 'daily'])->name('daily');
        Route::get('/create', [PresenceController::class, 'create'])->name('create');
        Route::post('/', [PresenceController::class, 'store'])->name('store');
        Route::get('/{presence}/edit', [PresenceController::class, 'edit'])->name('edit');
        Route::put('/{presence}', [PresenceController::class, 'update'])->name('update');
        Route::delete('/{presence}', [PresenceController::class, 'destroy'])->name('destroy');

        // Filtres et recherches
        Route::get('/filter', [PresenceController::class, 'filter'])->name('filter');
        Route::get('/export', [PresenceController::class, 'export'])->name('export');
    });

    // Module Gestion des congés
    Route::prefix('conges')->name('conges.')->group(function () {
        Route::get('/', [CongeController::class, 'index'])->name('index');
        Route::get('/dashboard', [CongeController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [CongeController::class, 'create'])->name('create');
        Route::post('/', [CongeController::class, 'store'])->name('store');
        Route::get('/{conge}', [CongeController::class, 'show'])->name('show');
        Route::get('/{conge}/edit', [CongeController::class, 'edit'])->name('edit');
        Route::put('/{conge}', [CongeController::class, 'update'])->name('update');
        Route::delete('/{conge}', [CongeController::class, 'destroy'])->name('destroy');

        // Interface agent
        Route::get('/mes-conges/list', [CongeController::class, 'mesConges'])->name('mes-conges');

        // Interface d'approbation directeur
        Route::get('/approbation-directeur/list', [CongeController::class, 'approbationDirecteur'])->name('approbation-directeur');
        Route::post('/{conge}/approuver-directeur', [CongeController::class, 'approuverDirecteur'])->name('approuver-directeur');

        // Interface de validation DRH
        Route::get('/validation-drh/list', [CongeController::class, 'validationDrh'])->name('validation-drh');
        Route::post('/{conge}/valider-drh', [CongeController::class, 'validerDrh'])->name('valider-drh');

        // API pour calcul de solde
        Route::get('/agent/{agent}/solde', [CongeController::class, 'calculerSolde'])->name('calculer-solde');
    });

    // Module Cotation des agents
    Route::prefix('cotations')->name('cotations.')->group(function () {
        Route::get('/', [CotationController::class, 'index'])->name('index');
        Route::get('/dashboard', [CotationController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [CotationController::class, 'create'])->name('create');
        Route::post('/', [CotationController::class, 'store'])->name('store');
        Route::get('/{cotation}', [CotationController::class, 'show'])->name('show');
        Route::get('/{cotation}/edit', [CotationController::class, 'edit'])->name('edit');
        Route::put('/{cotation}', [CotationController::class, 'update'])->name('update');
        Route::delete('/{cotation}', [CotationController::class, 'destroy'])->name('destroy');

        // API pour calcul en temps réel
        Route::post('/calculer', [CotationController::class, 'calculer'])->name('calculer');

        // Génération automatique
        Route::post('/generer-automatique', [CotationController::class, 'genererAutomatique'])->name('generer-automatique');
    });

    // Module Gestion des rôles et permissions
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('index');
        Route::get('/{role}', [RoleController::class, 'show'])->name('show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->name('update');

        // Gestion des agents et rôles
        Route::put('/agents/{agent}/role', [RoleController::class, 'updateAgentRole'])->name('update-agent-role');
        Route::post('/agents/{agent}/create-user-account', [RoleController::class, 'createUserAccount'])->name('create-user-account');
        Route::delete('/agents/{agent}/delete-user-account', [RoleController::class, 'deleteUserAccount'])->name('delete-user-account');
        Route::post('/agents/{agent}/reset-password', [RoleController::class, 'resetPassword'])->name('reset-password');

        // Agents par rôle
        Route::get('/{role}/agents', [RoleController::class, 'agentsByRole'])->name('agents-by-role');

        // Matrice des permissions
        Route::get('/permissions/matrix', [RoleController::class, 'permissions'])->name('permissions');
        Route::post('/permissions/update', [RoleController::class, 'updatePermissions'])->name('update-permissions');
    });

    // Module Gestion du Stock
    Route::prefix('stocks')->name('stocks.')->group(function () {
        Route::get('/', [StockController::class, 'index'])->name('index');
        Route::get('/dashboard', [StockController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [StockController::class, 'create'])->name('create');
        Route::post('/', [StockController::class, 'store'])->name('store');
        Route::get('/{stock}', [StockController::class, 'show'])->name('show');
        Route::get('/{stock}/edit', [StockController::class, 'edit'])->name('edit');
        Route::put('/{stock}', [StockController::class, 'update'])->name('update');
        Route::delete('/{stock}', [StockController::class, 'destroy'])->name('destroy');

        // Gestion des mouvements de stock
        Route::post('/{stock}/ajouter', [StockController::class, 'ajouterStock'])->name('ajouter');
        Route::post('/{stock}/retirer', [StockController::class, 'retirerStock'])->name('retirer');
        Route::get('/mouvements/list', [StockController::class, 'mouvements'])->name('mouvements');
    });

    // Module Demandes de Fournitures
    Route::prefix('demandes-fournitures')->name('demandes-fournitures.')->group(function () {
        Route::get('/', [DemandeFournitureController::class, 'index'])->name('index');
        Route::get('/dashboard', [DemandeFournitureController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [DemandeFournitureController::class, 'create'])->name('create');
        Route::post('/', [DemandeFournitureController::class, 'store'])->name('store');
        Route::get('/{demandeFourniture}', [DemandeFournitureController::class, 'show'])->name('show');
        Route::get('/{demandeFourniture}/edit', [DemandeFournitureController::class, 'edit'])->name('edit');
        Route::put('/{demandeFourniture}', [DemandeFournitureController::class, 'update'])->name('update');
        Route::delete('/{demandeFourniture}', [DemandeFournitureController::class, 'destroy'])->name('destroy');

        // Interface d'approbation
        Route::get('/approbation/list', [DemandeFournitureController::class, 'approbation'])->name('approbation');
        Route::post('/{demandeFourniture}/approuver', [DemandeFournitureController::class, 'approuver'])->name('approuver');
        Route::post('/{demandeFourniture}/livrer', [DemandeFournitureController::class, 'livrer'])->name('livrer');

        // Interface agent
        Route::get('/mes-demandes/list', [DemandeFournitureController::class, 'mesDemandes'])->name('mes-demandes');
    });

    // Module Gestion du Charroi Automobile

    // Gestion des véhicules
    Route::prefix('vehicules')->name('vehicules.')->group(function () {
        Route::get('/', [VehiculeController::class, 'index'])->name('index');
        Route::get('/dashboard', [VehiculeController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [VehiculeController::class, 'create'])->name('create');
        Route::post('/', [VehiculeController::class, 'store'])->name('store');
        Route::get('/{vehicule}', [VehiculeController::class, 'show'])->name('show');
        Route::get('/{vehicule}/edit', [VehiculeController::class, 'edit'])->name('edit');
        Route::put('/{vehicule}', [VehiculeController::class, 'update'])->name('update');
        Route::delete('/{vehicule}', [VehiculeController::class, 'destroy'])->name('destroy');

        // Maintenance des véhicules
        Route::get('/{vehicule}/maintenance', [VehiculeController::class, 'maintenance'])->name('maintenance');
        Route::post('/{vehicule}/maintenance', [VehiculeController::class, 'ajouterMaintenance'])->name('ajouter-maintenance');
        Route::post('/{vehicule}/changer-statut', [VehiculeController::class, 'changerStatut'])->name('changer-statut');
    });

    // Gestion des chauffeurs
    Route::prefix('chauffeurs')->name('chauffeurs.')->group(function () {
        Route::get('/', [ChauffeurController::class, 'index'])->name('index');
        Route::get('/create', [ChauffeurController::class, 'create'])->name('create');
        Route::post('/', [ChauffeurController::class, 'store'])->name('store');
        Route::get('/{chauffeur}', [ChauffeurController::class, 'show'])->name('show');
        Route::get('/{chauffeur}/edit', [ChauffeurController::class, 'edit'])->name('edit');
        Route::put('/{chauffeur}', [ChauffeurController::class, 'update'])->name('update');
        Route::delete('/{chauffeur}', [ChauffeurController::class, 'destroy'])->name('destroy');

        // API pour obtenir les chauffeurs disponibles
        Route::get('/disponibles/list', [ChauffeurController::class, 'disponibles'])->name('disponibles');
    });

    // Gestion des demandes de véhicules
    Route::prefix('demandes-vehicules')->name('demandes-vehicules.')->group(function () {
        Route::get('/', [DemandeVehiculeController::class, 'index'])->name('index');
        Route::get('/dashboard', [DemandeVehiculeController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [DemandeVehiculeController::class, 'create'])->name('create');
        Route::post('/', [DemandeVehiculeController::class, 'store'])->name('store');
        Route::get('/{demandeVehicule}', [DemandeVehiculeController::class, 'show'])->name('show');
        Route::get('/{demandeVehicule}/edit', [DemandeVehiculeController::class, 'edit'])->name('edit');
        Route::put('/{demandeVehicule}', [DemandeVehiculeController::class, 'update'])->name('update');
        Route::delete('/{demandeVehicule}', [DemandeVehiculeController::class, 'destroy'])->name('destroy');

        // Interface d'approbation
        Route::get('/approbation/list', [DemandeVehiculeController::class, 'approbation'])->name('approbation');
        Route::post('/{demandeVehicule}/approuver', [DemandeVehiculeController::class, 'approuver'])->name('approuver');

        // Interface d'affectation
        Route::get('/affectation/list', [DemandeVehiculeController::class, 'affectation'])->name('affectation');
        Route::post('/{demandeVehicule}/affecter', [DemandeVehiculeController::class, 'affecter'])->name('affecter');

        // Gestion des missions
        Route::post('/{demandeVehicule}/demarrer', [DemandeVehiculeController::class, 'demarrer'])->name('demarrer');
        Route::post('/{demandeVehicule}/terminer', [DemandeVehiculeController::class, 'terminer'])->name('terminer');

        // Interface agent
        Route::get('/mes-demandes/list', [DemandeVehiculeController::class, 'mesDemandes'])->name('mes-demandes');

        // API pour obtenir les informations d'affectation
        Route::get('/{demandeVehicule}/affectation', [DemandeVehiculeController::class, 'getAffectationInfo'])->name('get-affectation');
    });

    // API pour obtenir les véhicules disponibles
    Route::prefix('api')->group(function() {
        Route::get('/vehicules/disponibles', [VehiculeController::class, 'disponibles']);
        Route::get('/chauffeurs/disponibles', [ChauffeurController::class, 'disponibles']);
        Route::get('/demandes-vehicules/{demandeVehicule}/affectation', [DemandeVehiculeController::class, 'getAffectationInfo']);
    });

    // Module Gestion des paiements
    Route::prefix('paiements')->name('paiements.')->group(function () {
        Route::get('/', [PaiementController::class, 'index'])->name('index');
        Route::get('/dashboard', [PaiementController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [PaiementController::class, 'create'])->name('create');
        Route::post('/', [PaiementController::class, 'store'])->name('store');
        Route::get('/{paiement}', [PaiementController::class, 'show'])->name('show');
        Route::get('/{paiement}/edit', [PaiementController::class, 'edit'])->name('edit');
        Route::put('/{paiement}', [PaiementController::class, 'update'])->name('update');
        Route::delete('/{paiement}', [PaiementController::class, 'destroy'])->name('destroy');

        // Interface de validation
        Route::get('/validation/list', [PaiementController::class, 'validation'])->name('validation');
        Route::post('/{paiement}/valider', [PaiementController::class, 'valider'])->name('valider');

        // Interface de paiement
        Route::get('/paiement/list', [PaiementController::class, 'paiement'])->name('paiement');
        Route::post('/{paiement}/payer', [PaiementController::class, 'payer'])->name('payer');

        // Fiches de paie
        Route::get('/fiches-paie/list', [PaiementController::class, 'fichesPaie'])->name('fiches-paie');
        Route::get('/{paiement}/fiche-paie', [PaiementController::class, 'fichePaie'])->name('fiche-paie');

        // Interface agent
        Route::get('/mes-paiements/list', [PaiementController::class, 'mesPaiements'])->name('mes-paiements');

        // API pour calculs
        Route::get('/calculer-salaire', [PaiementController::class, 'calculerSalaire'])->name('calculer-salaire');
        Route::get('/calculer-decompte-final', [PaiementController::class, 'calculerDecompteFinal'])->name('calculer-decompte-final');
    });

    // Module Gestion des courriers
    Route::prefix('courriers')->name('courriers.')->group(function () {
        Route::get('/', [CourrierController::class, 'index'])->name('index');
        Route::get('/dashboard', [CourrierController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [CourrierController::class, 'create'])->name('create');
        Route::post('/', [CourrierController::class, 'store'])->name('store');
        Route::get('/{courrier}', [CourrierController::class, 'show'])->name('show');
        Route::get('/{courrier}/edit', [CourrierController::class, 'edit'])->name('edit');
        Route::put('/{courrier}', [CourrierController::class, 'update'])->name('update');
        Route::delete('/{courrier}', [CourrierController::class, 'destroy'])->name('destroy');

        // Traitement des courriers
        Route::post('/{courrier}/traiter', [CourrierController::class, 'traiter'])->name('traiter');
        Route::post('/{courrier}/archiver', [CourrierController::class, 'archiver'])->name('archiver');

        // Gestion des documents
        Route::post('/{courrier}/ajouter-document', [CourrierController::class, 'ajouterDocument'])->name('ajouter-document');
        Route::delete('/documents/{document}', [CourrierController::class, 'supprimerDocument'])->name('supprimer-document');

        // Vues spécifiques
        Route::get('/entrants/list', [CourrierController::class, 'entrants'])->name('entrants');
        Route::get('/sortants/list', [CourrierController::class, 'sortants'])->name('sortants');
        Route::get('/internes/list', [CourrierController::class, 'internes'])->name('internes');
        Route::get('/non-traites/list', [CourrierController::class, 'nonTraites'])->name('non-traites');
        Route::get('/archives/list', [CourrierController::class, 'archives'])->name('archives');
    });

    // Module Gestion des visiteurs
    Route::prefix('visitors')->name('visitors.')->group(function () {
        Route::get('/', [VisitorController::class, 'index'])->name('index');
        Route::get('/create', [VisitorController::class, 'create'])->name('create');
        Route::post('/', [VisitorController::class, 'store'])->name('store');
        Route::get('/{visitor}', [VisitorController::class, 'show'])->name('show');
        Route::get('/{visitor}/edit', [VisitorController::class, 'edit'])->name('edit');
        Route::put('/{visitor}', [VisitorController::class, 'update'])->name('update');
        Route::delete('/{visitor}', [VisitorController::class, 'destroy'])->name('destroy');

        // Marquer la sortie
        Route::post('/{visitor}/marquer-sortie', [VisitorController::class, 'marquerSortie'])->name('marquer-sortie');
    });

    // Module Gestion des valves (communiqués)
    Route::prefix('valves')->name('valves.')->group(function () {
        Route::get('/', [ValveController::class, 'index'])->name('index');
        Route::get('/dashboard', [ValveController::class, 'dashboard'])->name('dashboard');
        Route::get('/create', [ValveController::class, 'create'])->name('create');
        Route::post('/', [ValveController::class, 'store'])->name('store');
        Route::get('/{valve}', [ValveController::class, 'show'])->name('show');
        Route::get('/{valve}/edit', [ValveController::class, 'edit'])->name('edit');
        Route::put('/{valve}', [ValveController::class, 'update'])->name('update');
        Route::delete('/{valve}', [ValveController::class, 'destroy'])->name('destroy');

        // Activer/désactiver un communiqué
        Route::post('/{valve}/toggle-actif', [ValveController::class, 'toggleActif'])->name('toggle-actif');
    });
});