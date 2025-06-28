<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Agent::with(['role', 'user']);

        // Filtrage par rôle
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filtrage par statut de compte utilisateur
        if ($request->filled('user_status')) {
            if ($request->user_status === 'with_account') {
                $query->whereNotNull('user_id');
            } elseif ($request->user_status === 'without_account') {
                $query->whereNull('user_id');
            }
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

        $agents = $query->orderBy('nom')->paginate(15);

        // Statistiques
        $stats = [
            'total_agents' => Agent::count(),
            'with_accounts' => Agent::whereNotNull('user_id')->count(),
            'without_accounts' => Agent::whereNull('user_id')->count(),
            'active_users' => User::whereHas('agent')->count(),
        ];

        $roles = Role::where('is_active', true)->orderBy('display_name')->get();

        return view('roles.index', compact('agents', 'stats', 'roles'));
    }

    public function createUserAccount(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:6',
        ]);

        // Générer un mot de passe par défaut si non fourni
        $password = $validated['password'] ?? 'password123';

        // Créer l'utilisateur
        $user = User::create([
            'name' => $agent->full_name,
            'email' => $validated['email'],
            'password' => Hash::make($password),
        ]);

        // Associer l'agent à l'utilisateur
        $agent->update(['user_id' => $user->id]);

        return back()->with('success', 'Compte utilisateur créé avec succès. Mot de passe par défaut : ' . $password);
    }

    public function updateAgentRole(Request $request, Agent $agent)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $agent->update($validated);

        return back()->with('success', 'Rôle de l\'agent mis à jour avec succès.');
    }

    public function permissions(Request $request)
    {
        $query = Role::where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%");
            });
        }

        $roles = $query->orderBy('display_name')->get();
        $availablePermissions = Role::getAvailablePermissions();

        // Grouper les permissions par catégorie
        $groupedPermissions = [];
        foreach ($availablePermissions as $permission => $description) {
            $category = explode('.', $permission)[0];
            $groupedPermissions[$category][$permission] = $description;
        }

        return view('roles.permissions', compact('roles', 'groupedPermissions'));
    }

    public function updatePermissions(Request $request)
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'array',
        ]);

        foreach ($validated['permissions'] as $roleId => $permissions) {
            $role = Role::find($roleId);
            if ($role) {
                $grantedPermissions = array_keys(array_filter($permissions));
                $role->syncPermissions($grantedPermissions);
            }
        }

        return back()->with('success', 'Permissions mises à jour avec succès.');
    }

    public function show(Role $role)
    {
        $role->load('agents.user');
        $availablePermissions = Role::getAvailablePermissions();

        return view('roles.show', compact('role', 'availablePermissions'));
    }

    public function edit(Role $role)
    {
        $availablePermissions = Role::getAvailablePermissions();

        // Grouper les permissions par catégorie
        $groupedPermissions = [];
        foreach ($availablePermissions as $permission => $description) {
            $category = explode('.', $permission)[0];
            $groupedPermissions[$category][$permission] = $description;
        }

        return view('roles.edit', compact('role', 'groupedPermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'permissions' => 'array',
            'permissions.*' => 'string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['permissions'] = $validated['permissions'] ?? [];

        $role->update($validated);

        return redirect()->route('roles.index')
            ->with('success', 'Rôle mis à jour avec succès.');
    }

    public function deleteUserAccount(Agent $agent)
    {
        if ($agent->user) {
            $agent->user->delete();
            $agent->update(['user_id' => null]);
            
            return back()->with('success', 'Compte utilisateur supprimé avec succès.');
        }

        return back()->with('error', 'Aucun compte utilisateur à supprimer.');
    }

    public function resetPassword(Agent $agent)
    {
        if (!$agent->user) {
            return back()->with('error', 'Aucun compte utilisateur associé.');
        }

        $newPassword = 'password123';
        $agent->user->update([
            'password' => Hash::make($newPassword)
        ]);

        return back()->with('success', 'Mot de passe réinitialisé. Nouveau mot de passe : ' . $newPassword);
    }

    public function agentsByRole(Role $role)
    {
        $agents = $role->agents()->with('user')->orderBy('nom')->paginate(15);
        
        return view('roles.agents-by-role', compact('role', 'agents'));
    }
}