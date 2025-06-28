<?php

namespace App\Http\Controllers;

use App\Models\Conge;
use App\Models\Agent;
use App\Models\SoldeConge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CongeController extends Controller
{
    public function index(Request $request)
    {
        $query = Conge::with(['agent', 'approbateurDirecteur', 'validateurDrh']);

        // Filtrage par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtrage par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtrage par agent
        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        // Filtrage par période
        if ($request->filled('date_debut')) {
            $query->where('date_debut', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('date_fin', '<=', $request->date_fin);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('agent', function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenoms', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%");
            });
        }

        $conges = $query->orderBy('created_at', 'desc')->paginate(10);

        // Statistiques
        $stats = [
            'total' => Conge::count(),
            'en_attente' => Conge::enAttente()->count(),
            'approuve_directeur' => Conge::approuveDirecteur()->count(),
            'valide_drh' => Conge::valide()->count(),
            'en_cours' => Conge::enCours()->count(),
            'rejete' => Conge::rejete()->count(),
        ];

        $agents = Agent::where('statut', 'actif')->orderBy('nom')->get();

        return view('conges.index', compact('conges', 'stats', 'agents'));
    }

    public function dashboard()
    {
        $stats = [
            'total' => Conge::count(),
            'en_attente' => Conge::enAttente()->count(),
            'approuve_directeur' => Conge::approuveDirecteur()->count(),
            'valide_drh' => Conge::valide()->count(),
            'en_cours' => Conge::enCours()->count(),
            'rejete' => Conge::rejete()->count(),
        ];

        // Congés en cours
        $congesEnCours = Conge::enCours()
            ->with('agent')
            ->orderBy('date_debut')
            ->take(10)
            ->get();

        // Demandes en attente
        $demandesEnAttente = Conge::enAttente()
            ->with('agent')
            ->orderBy('created_at')
            ->take(10)
            ->get();

        // Statistiques par type
        $statsParType = [
            'annuel' => Conge::where('type', 'annuel')->count(),
            'maladie' => Conge::where('type', 'maladie')->count(),
            'maternite' => Conge::where('type', 'maternite')->count(),
            'paternite' => Conge::where('type', 'paternite')->count(),
            'exceptionnel' => Conge::where('type', 'exceptionnel')->count(),
        ];

        // Agents éligibles et non éligibles
        $agentsActifs = Agent::where('statut', 'actif')->get();
        $agentsEligibles = 0;
        $agentsNonEligibles = 0;

        foreach ($agentsActifs as $agent) {
            $solde = SoldeConge::calculerSolde($agent);
            if ($solde['jours_acquis'] > 0) {
                $agentsEligibles++;
            } else {
                $agentsNonEligibles++;
            }
        }

        return view('conges.dashboard', compact(
            'stats',
            'congesEnCours',
            'demandesEnAttente',
            'statsParType',
            'agentsEligibles',
            'agentsNonEligibles'
        ));
    }

    public function create()
    {
        $agents = Agent::where('statut', 'actif')->orderBy('nom')->get();
        return view('conges.create', compact('agents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after:date_debut',
            'type' => 'required|in:annuel,maladie,maternite,paternite,exceptionnel',
            'motif' => 'required|string|max:1000',
            'justificatif' => 'nullable|image|mimes:jpeg,png,jpg,gif,pdf|max:2048',
        ]);

        $agent = Agent::findOrFail($validated['agent_id']);

        // Calculer le nombre de jours
        $nombreJours = Conge::calculerNombreJours($validated['date_debut'], $validated['date_fin']);

        // Vérifier le solde pour les congés annuels
        if ($validated['type'] === 'annuel') {
            $solde = SoldeConge::calculerSolde($agent);
            if ($solde['jours_restants'] < $nombreJours) {
                return back()->withErrors([
                    'date_fin' => "Solde insuffisant. Jours disponibles: {$solde['jours_restants']}, demandés: {$nombreJours}"
                ]);
            }
        }

        // Vérifier les chevauchements
        $chevauchement = Conge::where('agent_id', $validated['agent_id'])
            ->where('statut', '!=', 'rejete')
            ->where(function($query) use ($validated) {
                $query->whereBetween('date_debut', [$validated['date_debut'], $validated['date_fin']])
                      ->orWhereBetween('date_fin', [$validated['date_debut'], $validated['date_fin']])
                      ->orWhere(function($q) use ($validated) {
                          $q->where('date_debut', '<=', $validated['date_debut'])
                            ->where('date_fin', '>=', $validated['date_fin']);
                      });
            })
            ->exists();

        if ($chevauchement) {
            return back()->withErrors([
                'date_debut' => 'Cette période chevauche avec un congé existant.'
            ]);
        }

        $validated['nombre_jours'] = $nombreJours;

        // Gestion de l'upload du justificatif
        if ($request->hasFile('justificatif')) {
            $justificatifPath = $request->file('justificatif')->store('conges/justificatifs', 'public');
            $validated['justificatif'] = $justificatifPath;
        }

        Conge::create($validated);

        return redirect()->route('conges.index')
            ->with('success', 'Demande de congé créée avec succès.');
    }

    public function show(Conge $conge)
    {
        $conge->load(['agent', 'approbateurDirecteur', 'validateurDrh']);
        return view('conges.show', compact('conge'));
    }

    public function edit(Conge $conge)
    {
        if (!$conge->peutEtreModifie()) {
            return redirect()->route('conges.show', $conge)
                ->with('error', 'Cette demande ne peut plus être modifiée.');
        }

        $agent = Agent::find($conge->agent_id);
        return view('conges.edit', compact('conge', 'agent'));
    }

    public function update(Request $request, Conge $conge)
    {
        if (!$conge->peutEtreModifie()) {
            return redirect()->route('conges.show', $conge)
                ->with('error', 'Cette demande ne peut plus être modifiée.');
        }

        $validated = $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after:date_debut',
            'type' => 'required|in:annuel,maladie,maternite,paternite,exceptionnel',
            'motif' => 'required|string|max:1000',
            'justificatif' => 'nullable|image|mimes:jpeg,png,jpg,gif,pdf|max:2048',
        ]);

        $agent = Agent::findOrFail($validated['agent_id']);
        $nombreJours = Conge::calculerNombreJours($validated['date_debut'], $validated['date_fin']);

        // Vérifier le solde pour les congés annuels
        if ($validated['type'] === 'annuel') {
            $solde = SoldeConge::calculerSolde($agent);
            // Ajouter les jours du congé actuel au solde disponible
            $joursDisponibles = $solde['jours_restants'] + ($conge->type === 'annuel' ? $conge->nombre_jours : 0);

            if ($joursDisponibles < $nombreJours) {
                return back()->withErrors([
                    'date_fin' => "Solde insuffisant. Jours disponibles: {$joursDisponibles}, demandés: {$nombreJours}"
                ]);
            }
        }

        $validated['nombre_jours'] = $nombreJours;

        // Gestion de l'upload du justificatif
        if ($request->hasFile('justificatif')) {
            // Supprimer l'ancien justificatif si existant
            if ($conge->justificatif && Storage::disk('public')->exists($conge->justificatif)) {
                Storage::disk('public')->delete($conge->justificatif);
            }

            $justificatifPath = $request->file('justificatif')->store('conges/justificatifs', 'public');
            $validated['justificatif'] = $justificatifPath;
        }

        $conge->update($validated);

        if (Auth::user()->role->name == 'rh' || Auth::user()->role->name == 'drh') {
            $conge->statut = 'traiter_rh';
            $conge->save();
        }

        return redirect()->route('conges.show', $conge)
            ->with('success', 'Demande de congé modifiée avec succès.');
    }

    public function destroy(Conge $conge)
    {
        if (!$conge->peutEtreModifie()) {
            return redirect()->route('conges.index')
                ->with('error', 'Cette demande ne peut pas être supprimée.');
        }

        // Supprimer le justificatif si existant
        if ($conge->justificatif && Storage::disk('public')->exists($conge->justificatif)) {
            Storage::disk('public')->delete($conge->justificatif);
        }

        $conge->delete();

        return redirect()->route('conges.index')
            ->with('success', 'Demande de congé supprimée avec succès.');
    }

    // Interface d'approbation directeur
    public function approbationDirecteur(Request $request)
    {
        $query = Conge::with('agent')->enAttente();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('agent', function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenoms', 'like', "%{$search}%");
            });
        }

        $conges = $query->orderBy('created_at')->paginate(10);

        return view('conges.approbation-directeur', compact('conges'));
    }

    public function approuverDirecteur(Request $request, Conge $conge)
    {
        if (!$conge->peutEtreApprouveParDirecteur()) {
            return back()->with('error', 'Cette demande ne peut pas être approuvée.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approuver,rejeter',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        if ($validated['action'] === 'approuver') {
            $conge->update([
                'statut' => 'approuve_directeur',
                'commentaire_directeur' => $validated['commentaire'],
                'date_approbation_directeur' => now(),
                'approuve_par_directeur' => Auth::id(),
            ]);

            $message = 'Demande approuvée avec succès.';
        } else {
            $conge->update([
                'statut' => 'rejete',
                'commentaire_directeur' => $validated['commentaire'],
                'date_approbation_directeur' => now(),
                'approuve_par_directeur' => Auth::id(),
            ]);

            $message = 'Demande rejetée.';
        }

        return back()->with('success', $message);
    }

    // Interface de validation DRH
    public function validationDrh(Request $request)
    {
        $query = Conge::with('agent')->traiterRh();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('agent', function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenoms', 'like', "%{$search}%");
            });
        }

        $conges = $query->orderBy('date_approbation_directeur')->paginate(10);

        return view('conges.validation-drh', compact('conges'));
    }

    public function validerDrh(Request $request, Conge $conge)
    {
        if (!$conge->peutEtreValideParDrh()) {
            return back()->with('error', 'Cette demande ne peut pas être validée.');
        }

        $validated = $request->validate([
            'action' => 'required|in:valider,rejeter',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        if ($validated['action'] === 'valider') {
            $conge->update([
                'statut' => 'valide_drh',
                'commentaire_drh' => $validated['commentaire'],
                'date_validation_drh' => now(),
                'valide_par_drh' => Auth::id(),
            ]);

            // Mettre à jour le solde de congé
            SoldeConge::mettreAJourSolde($conge->agent);

            $message = 'Demande validée avec succès.';
        } else {
            $conge->update([
                'statut' => 'rejete',
                'commentaire_drh' => $validated['commentaire'],
                'date_validation_drh' => now(),
                'valide_par_drh' => Auth::id(),
            ]);

            $message = 'Demande rejetée.';
        }

        return back()->with('success', $message);
    }

    // Interface agent pour ses propres demandes
    public function mesConges()
    {
        $user = Auth::user();

        if (!$user->agent) {
            return redirect()->route('dashboard')
                ->with('error', 'Vous devez être associé à un agent pour accéder à cette page.');
        }

        $conges = Conge::where('agent_id', $user->agent->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculer le solde
        $solde = SoldeConge::calculerSolde($user->agent);

        return view('conges.mes-conges', compact('conges', 'solde'));
    }

    public function calculerSolde(Agent $agent)
    {
        return response()->json(SoldeConge::calculerSolde($agent));
    }
}
