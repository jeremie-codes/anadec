<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    public function index(Request $request)
    {
        $query = Agent::query();

        // Filtrage par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtrage par direction
        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenoms', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%");
            });
        }

        $agents = $query->orderBy('nom')->paginate(10);

        // Statistiques
        $stats = [
            'total' => Agent::count(),
            'actifs' => Agent::where('statut', 'actif')->count(),
            'retraites' => Agent::where('statut', 'retraite')->count(),
            'malades' => Agent::where('statut', 'malade')->count(),
            'demissions' => Agent::where('statut', 'demission')->count(),
        ];

        return view('agents.index', compact('agents', 'stats'));
    }

    public function create()
    {
        return view('agents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'matricule' => 'required|string|unique:agents',
            'nom' => 'required|string|max:255',
            'prenoms' => 'required|string|max:255',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string|max:255',
            'sexe' => 'required|in:M,F',
            'situation_matrimoniale' => 'required|string',
            'direction' => 'required|string',
            'service' => 'required|string',
            'poste' => 'required|string',
            'date_recrutement' => 'required|date',
            'telephone' => 'nullable|string',
            'email' => 'nullable|email',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'adresse' => 'nullable|string',

            // Champs pour la création du compte utilisateur
            'create_user_account' => 'nullable|boolean',
            'user_email' => 'nullable|email|unique:users,email',
            'user_password' => 'nullable|string|min:6',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $validated['statut'] = 'actif';

        // Gestion de l'upload de photo
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('agents/photos', 'public');
            $validated['photo'] = $photoPath;
        }

        // Créer l'agent
        $agent = Agent::create($validated);

        // Créer le compte utilisateur si demandé
        if ($request->boolean('create_user_account')) {
            $this->createUserAccount($agent, $validated);
        }

        return redirect()->route('agents.index')
            ->with('success', 'Agent créé avec succès.' .
                ($request->boolean('create_user_account') ? ' Un compte utilisateur a également été créé.' : ''));
    }

    private function createUserAccount(Agent $agent, array $validated)
    {
        // Déterminer l'email à utiliser
        $userEmail = $validated['user_email'] ?? $validated['email'] ?? null;

        if (!$userEmail) {
            // Générer un email basé sur le nom si aucun email n'est fourni
            $userEmail = strtolower(Str::slug($agent->prenoms . '.' . $agent->nom)) . '@anadec.com';
        }

        // Déterminer le mot de passe
        $password = $validated['user_password'] ?? 'password';

        // Créer l'utilisateur
        $user = User::create([
            'name' => $agent->full_name,
            'email' => $userEmail,
            'password' => Hash::make($password),
            'role_id' => $validated['role_id'] ?? null,
        ]);

        // Associer l'agent à l'utilisateur
        $agent->update(['user_id' => $user->id]);

        return $user;
    }

    public function show(Agent $agent)
    {
        return view('agents.show', compact('agent'));
    }

    public function edit(Agent $agent)
    {
        return view('agents.edit', compact('agent'));
    }

    public function update(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'matricule' => 'required|string|unique:agents,matricule,' . $agent->id,
            'nom' => 'required|string|max:255',
            'prenoms' => 'required|string|max:255',
            'date_naissance' => 'required|date',
            'lieu_naissance' => 'required|string|max:255',
            'sexe' => 'required|in:M,F',
            'situation_matrimoniale' => 'required|string',
            'direction' => 'required|string',
            'service' => 'required|string',
            'poste' => 'required|string',
            'date_recrutement' => 'required|date',
            'telephone' => 'nullable|string',
            'email' => 'nullable|email',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'adresse' => 'nullable|string',
            'statut' => 'required|string',
        ]);

        // Gestion de l'upload de photo
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($agent->photo && Storage::disk('public')->exists($agent->photo)) {
                Storage::disk('public')->delete($agent->photo);
            }

            $photoPath = $request->file('photo')->store('agents/photos', 'public');
            $validated['photo'] = $photoPath;
        }

        $agent->update($validated);

               // Créer le compte utilisateur si demandé
        if ($request->boolean('create_user_account')) {
            $this->createUserAccount($agent, $validated);
        }

        return redirect()->route('agents.index')
            ->with('success', 'Agent modifié avec succès.');
    }

    public function destroy(Agent $agent)
    {
        // Supprimer la photo si elle existe
        if ($agent->photo && Storage::disk('public')->exists($agent->photo)) {
            Storage::disk('public')->delete($agent->photo);
        }

        $agent->delete();

        return redirect()->route('agents.index')
            ->with('success', 'Agent supprimé avec succès.');
    }

    // Méthodes pour les sous-modules
    public function identification(Request $request)
    {
        $agents = Agent::where('statut', 'actif')
            ->when($request->search, function($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenoms', 'like', "%{$search}%")
                      ->orWhere('matricule', 'like', "%{$search}%");
            })
            ->orderBy('nom')
            ->paginate(10);

        return view('agents.identification', compact('agents'));
    }

    public function retraites(Request $request)
    {
        $agents = Agent::where('statut', 'retraite')
            ->when($request->search, function($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenoms', 'like', "%{$search}%");
            })
            ->orderBy('date_retraite', 'desc')
            ->paginate(10);

        return view('agents.retraites', compact('agents'));
    }

    public function malades(Request $request)
    {
        $agents = Agent::where('statut', 'malade')
            ->when($request->search, function($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenoms', 'like', "%{$search}%");
            })
            ->orderBy('date_maladie', 'desc')
            ->paginate(10);

        return view('agents.malades', compact('agents'));
    }

    public function demissions(Request $request)
    {
        $agents = Agent::where('statut', 'demission')
            ->when($request->search, function($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenoms', 'like', "%{$search}%");
            })
            ->orderBy('date_demission', 'desc')
            ->paginate(10);

        return view('agents.demissions', compact('agents'));
    }

    public function revocations(Request $request)
    {
        $agents = Agent::where('statut', 'revocation')
            ->when($request->search, function($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenoms', 'like', "%{$search}%");
            })
            ->orderBy('date_revocation', 'desc')
            ->paginate(10);

        return view('agents.revocations', compact('agents'));
    }

    public function disponibilites(Request $request)
    {
        $agents = Agent::where('statut', 'disponibilite')
            ->when($request->search, function($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenoms', 'like', "%{$search}%");
            })
            ->orderBy('date_disponibilite', 'desc')
            ->paginate(10);

        return view('agents.disponibilites', compact('agents'));
    }

    public function detachements(Request $request)
    {
        $agents = Agent::where('statut', 'detachement')
            ->when($request->search, function($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenoms', 'like', "%{$search}%");
            })
            ->orderBy('date_detachement', 'desc')
            ->paginate(10);

        return view('agents.detachements', compact('agents'));
    }

    public function mutations(Request $request)
    {
        $agents = Agent::where('statut', 'mutation')
            ->when($request->search, function($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenoms', 'like', "%{$search}%");
            })
            ->orderBy('date_mutation', 'desc')
            ->paginate(10);

        return view('agents.mutations', compact('agents'));
    }

    public function reintegrations(Request $request)
    {
        $agents = Agent::where('statut', 'reintegration')
            ->when($request->search, function($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenoms', 'like', "%{$search}%");
            })
            ->orderBy('date_reintegration', 'desc')
            ->paginate(10);

        return view('agents.reintegrations', compact('agents'));
    }

    public function missions(Request $request)
    {
        $agents = Agent::where('statut', 'mission')
            ->when($request->search, function($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenoms', 'like', "%{$search}%");
            })
            ->orderBy('date_mission', 'desc')
            ->paginate(10);

        return view('agents.missions', compact('agents'));
    }

    public function deces(Request $request)
    {
        $agents = Agent::where('statut', 'deces')
            ->when($request->search, function($query, $search) {
                $query->where('nom', 'like', "%{$search}%")
                      ->orWhere('prenoms', 'like', "%{$search}%");
            })
            ->orderBy('date_deces', 'desc')
            ->paginate(10);

        return view('agents.deces', compact('agents'));
    }
}
