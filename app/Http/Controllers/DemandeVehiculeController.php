<?php

namespace App\Http\Controllers;

use App\Models\DemandeVehicule;
use App\Models\Agent;
use App\Models\Vehicule;
use App\Models\Chauffeur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemandeVehiculeController extends Controller
{
    public function index(Request $request)
    {
        $query = DemandeVehicule::with(['agent', 'vehicule', 'chauffeur', 'approbateur']);

        // Filtrage par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtrage par urgence
        if ($request->filled('urgence')) {
            $query->where('urgence', $request->urgence);
        }

        // Filtrage par direction
        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }

        // Filtrage par période
        if ($request->filled('date_debut')) {
            $query->whereDate('date_heure_sortie', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_heure_sortie', '<=', $request->date_fin);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('destination', 'like', "%{$search}%")
                  ->orWhere('motif', 'like', "%{$search}%")
                  ->orWhereHas('agent', function($subQ) use ($search) {
                      $subQ->where('nom', 'like', "%{$search}%")
                           ->orWhere('prenoms', 'like', "%{$search}%");
                  });
            });
        }

        $demandes = $query->orderBy('created_at', 'desc')->paginate(10);

        // Statistiques
        $stats = [
            'total' => DemandeVehicule::count(),
            'en_attente' => DemandeVehicule::enAttente()->count(),
            'approuve' => DemandeVehicule::approuve()->count(),
            'affecte' => DemandeVehicule::affecte()->count(),
            'en_cours' => DemandeVehicule::enCours()->count(),
            'termine' => DemandeVehicule::termine()->count(),
            'rejete' => DemandeVehicule::rejete()->count(),
            'urgent' => DemandeVehicule::urgent()->count(),
        ];

        return view('demandes-vehicules.index', compact('demandes', 'stats'));
    }

    public function dashboard()
    {
        $stats = [
            'total' => DemandeVehicule::count(),
            'en_attente' => DemandeVehicule::enAttente()->count(),
            'approuve' => DemandeVehicule::approuve()->count(),
            'affecte' => DemandeVehicule::affecte()->count(),
            'en_cours' => DemandeVehicule::enCours()->count(),
            'termine' => DemandeVehicule::termine()->count(),
            'rejete' => DemandeVehicule::rejete()->count(),
            'urgent' => DemandeVehicule::urgent()->count(),
        ];

        // Demandes urgentes
        $demandesUrgentes = DemandeVehicule::urgent()
            ->with('agent')
            ->whereIn('statut', ['en_attente', 'approuve', 'affecte', 'en_cours'])
            ->orderBy('urgence', 'desc')
            ->orderBy('created_at')
            ->take(10)
            ->get();

        // Demandes en cours
        $demandesEnCours = DemandeVehicule::enCours()
            ->with(['agent', 'vehicule', 'chauffeur'])
            ->orderBy('date_heure_sortie')
            ->take(10)
            ->get();

        // Véhicules en mission
        $vehiculesEnMission = Vehicule::whereHas('demandesVehicules', function($query) {
            $query->where('statut', 'en_cours');
        })->with(['demandesVehicules' => function($query) {
            $query->where('statut', 'en_cours')->with('agent');
        }])->get();

        return view('demandes-vehicules.dashboard', compact(
            'stats',
            'demandesUrgentes',
            'demandesEnCours',
            'vehiculesEnMission'
        ));
    }

    public function create()
    {
        $agents = Agent::where('statut', 'actif')->orderBy('nom')->get();

        // Directions prédéfinies
        $directions = [
            'Direction Générale',
            'Direction RH',
            'Direction Financière',
            'Direction Technique',
            'Direction Administrative',
            'Direction Commerciale'
        ];

        $chauffeurs = Chauffeur::all() ?? [];

        $vehicules = Vehicule::where('disponible', true)->get() ?? [];

        return view('demandes-vehicules.create', compact('agents', 'directions', 'chauffeurs', 'vehicules'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'vehicule_id' => 'required|exists:vehicules,id',
            'destination' => 'required|string|max:255',
            'motif' => 'required|string|max:1000',
            'date_heure_sortie' => 'required|date|after_or_equal:today',
            'date_heure_retour_prevue' => 'required|date|after:date_heure_sortie',
            'nombre_passagers' => 'required|integer|min:1|max:50',
            'urgence' => 'required|in:faible,normale,elevee,critique',
        ]);

        // dd($validated);

        DemandeVehicule::create($validated);

        return redirect()->route('demandes-vehicules.index')
            ->with('success', 'Demande de véhicule créée avec succès.');
        } catch (\Throwable $th) {
            return redirect()->route('demandes-vehicules.create')
                ->with('error', 'Error :' . $th->getMessage());
        }
    }

    public function show(DemandeVehicule $demandeVehicule)
    {
        $demandeVehicule->load(['agent', 'vehicule', 'chauffeur', 'approbateur']);
        return view('demandes-vehicules.show', compact('demandeVehicule'));
    }

    public function edit(DemandeVehicule $demandeVehicule)
    {
        if (!$demandeVehicule->peutEtreModifie()) {
            return redirect()->route('demandes-vehicules.show', $demandeVehicule)
                ->with('error', 'Cette demande ne peut plus être modifiée.');
        }

        $agents = Agent::where('statut', 'actif')->orderBy('nom')->get();
        $chauffeurs = Chauffeur::all();

        $directions = [
            'Direction Générale',
            'Direction RH',
            'Direction Financière',
            'Direction Technique',
            'Direction Administrative',
            'Direction Commerciale'
        ];

        return view('demandes-vehicules.edit', compact('demandeVehicule', 'agents', 'directions', 'chauffeurs'));
    }

    public function update(Request $request, DemandeVehicule $demandeVehicule)
    {
        if (!$demandeVehicule->peutEtreModifie()) {
            return redirect()->route('demandes-vehicules.show', $demandeVehicule)
                ->with('error', 'Cette demande ne peut plus être modifiée.');
        }

        $validated = $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'chauffeur_id' => 'required|exists:chauffeurs,id',
            'destination' => 'required|string|max:255',
            'motif' => 'required|string|max:1000',
            'date_heure_sortie' => 'required|date|after_or_equal:today',
            'date_heure_retour_prevue' => 'required|date|after:date_heure_sortie',
            'nombre_passagers' => 'required|integer|min:1|max:50',
            'urgence' => 'required|in:faible,normale,elevee,critique',
        ]);

        $demandeVehicule->update($validated);

        return redirect()->route('demandes-vehicules.show', $demandeVehicule)
            ->with('success', 'Demande de véhicule modifiée avec succès.');
    }

    public function destroy(DemandeVehicule $demandeVehicule)
    {
        if (!$demandeVehicule->peutEtreModifie()) {
            return redirect()->route('demandes-vehicules.index')
                ->with('error', 'Cette demande ne peut pas être supprimée.');
        }

        $demandeVehicule->delete();

        return redirect()->route('demandes-vehicules.index')
            ->with('success', 'Demande de véhicule supprimée avec succès.');
    }

    public function approbation(Request $request)
    {
        $query = DemandeVehicule::with('agent')->enAttente();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('destination', 'like', "%{$search}%")
                  ->orWhere('motif', 'like', "%{$search}%")
                  ->orWhereHas('agent', function($subQ) use ($search) {
                      $subQ->where('nom', 'like', "%{$search}%")
                           ->orWhere('prenoms', 'like', "%{$search}%");
                  });
            });
        }

        $demandes = $query->orderBy('urgence', 'desc')
                         ->orderBy('created_at')
                         ->paginate(10);

        return view('demandes-vehicules.approbation', compact('demandes'));
    }

    public function approuver(Request $request, DemandeVehicule $demandeVehicule)
    {
        if (!$demandeVehicule->peutEtreApprouve()) {
            return back()->with('error', 'Cette demande ne peut pas être approuvée.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approuver,rejeter',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        if ($validated['action'] === 'approuver') {
            $demandeVehicule->update([
                'statut' => 'approuve',
                'commentaire_approbateur' => $validated['commentaire'],
                'date_approbation' => now(),
                'approuve_par' => Auth::id(),
            ]);

            $message = 'Demande approuvée avec succès.';
        } else {
            $demandeVehicule->update([
                'statut' => 'rejete',
                'commentaire_approbateur' => $validated['commentaire'],
                'date_approbation' => now(),
                'approuve_par' => Auth::id(),
            ]);

            $message = 'Demande rejetée.';
        }

        return back()->with('success', $message);
    }

    public function affectation(Request $request)
    {
        $query = DemandeVehicule::with('agent')->approuve();

        $demandes = $query->orderBy('urgence', 'desc')
                         ->orderBy('date_heure_sortie')
                         ->paginate(10);

        // Véhicules disponibles
        $vehicules = Vehicule::disponible()->get();
        $vehiculesDisponibles = $vehicules->count();

        // Chauffeurs disponibles
        $chauffeurs = Chauffeur::disponible()->get();
        $chauffeursDisponibles = $chauffeurs->count();

        return view('demandes-vehicules.affectation', compact(
            'demandes',
            'vehicules',
            'chauffeurs',
            'vehiculesDisponibles',
            'chauffeursDisponibles'
        ));
    }

    public function affecter(Request $request, DemandeVehicule $demandeVehicule)
    {
        if (!$demandeVehicule->peutEtreAffecte()) {
            return back()->with('error', 'Cette demande ne peut pas être affectée.');
        }

        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'chauffeur_id' => 'required|exists:chauffeurs,id',
            'commentaire_affectation' => 'nullable|string|max:1000',
        ]);

        // Vérifier la disponibilité du véhicule et du chauffeur
        $vehicule = Vehicule::findOrFail($validated['vehicule_id']);
        $chauffeur = Chauffeur::findOrFail($validated['chauffeur_id']);

        if (!$vehicule->estDisponible($demandeVehicule->date_heure_sortie, $demandeVehicule->date_heure_retour_prevue)) {
            return back()->with('error', 'Le véhicule sélectionné n\'est pas disponible pour cette période.');
        }

        if (!$chauffeur->estDisponible($demandeVehicule->date_heure_sortie, $demandeVehicule->date_heure_retour_prevue)) {
            return back()->with('error', 'Le chauffeur sélectionné n\'est pas disponible pour cette période.');
        }

        $demandeVehicule->update([
            'statut' => 'affecte',
            'vehicule_id' => $validated['vehicule_id'],
            'chauffeur_id' => $validated['chauffeur_id'],
            'commentaire_affectation' => $validated['commentaire_affectation'],
            'date_affectation' => now(),
        ]);

        return back()->with('success', 'Véhicule et chauffeur affectés avec succès.');
    }

    public function mesDemandes()
    {
        $user = Auth::user();

        if (!$user->agent) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous devez être associé à un agent pour accéder à cette page.');
        }

        $demandes = DemandeVehicule::where('agent_id', $user->agent->id)
            ->with(['vehicule', 'chauffeur'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('demandes-vehicules.mes-demandes', compact('demandes'));
    }
}
