<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('name')->get();
        $availablePermissions = Role::getAvailablePermissions();

        // Grouper les permissions par catégorie
        $groupedPermissions = [];
        foreach ($availablePermissions as $permission => $description) {
            $category = explode('.', $permission)[0];
            $groupedPermissions[$category][$permission] = $description;
        }

        return view('roles.index', compact('roles', 'groupedPermissions'));
    }

    public function show(Role $role)
    {
        $role->load('users');
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

    public function users(Request $request)
    {
        $query = User::with('role');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            if ($request->role === 'no_role') {
                $query->whereNull('role_id');
            } else {
                $query->where('role_id', $request->role);
            }
        }

        $users = $query->orderBy('name')->paginate(10);
        $roles = Role::where('is_active', true)->withCount('users')->orderBy('display_name')->get();

        return view('roles.users', compact('users', 'roles'));
    }

    public function updateUserRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $user->update($validated);

        return back()->with('success', 'Rôle de l\'utilisateur mis à jour avec succès.');
    }

    public function permissions()
    {
        $availablePermissions = Role::getAvailablePermissions();
        $roles = Role::where('is_active', true)->get();

        $permissionMatrix = [];
        foreach ($availablePermissions as $permission => $description) {
            $permissionMatrix[$permission] = [
                'description' => $description,
                'roles' => []
            ];

            foreach ($roles as $role) {
                $permissionMatrix[$permission]['roles'][$role->id] = $role->hasPermission($permission);
            }
        }

        $groupedMatrix = [];
        foreach ($permissionMatrix as $permission => $data) {
            $category = explode('.', $permission)[0];
            $groupedMatrix[$category][$permission] = $data;
        }

        return view('roles.permissions', compact('groupedMatrix', 'roles'));
    }

    public function updatePermissions(Request $request)
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'array',
            'permissions.*.*' => 'boolean',
        ]);

        $roles = Role::all()->keyBy('id');

        foreach ($validated['permissions'] as $roleId => $permissions) {
            if (isset($roles[$roleId])) {
                $grantedPermissions = array_keys(array_filter($permissions));
                $roles[$roleId]->syncPermissions($grantedPermissions);
            }
        }

        return back()->with('success', 'Permissions mises à jour avec succès.');
    }

    // ========================== NOUVELLES MÉTHODES =============================

    public function userPermissions()
    {
        $availablePermissions = Role::getAvailablePermissions();
        $roles = Role::all();
        $users = User::with(['agent.role'])->get();

        $permissionMatrix = [];
        foreach ($availablePermissions as $permission => $description) {
            $permissionMatrix[$permission] = [
                'description' => $description,
                'users' => []
            ];

            foreach ($users as $user) {
                $permissionMatrix[$permission]['users'][$user->id] = $user->hasPermission($permission);
            }
        }

        $groupedMatrix = [];
        foreach ($users as $user) {
            foreach ($permissionMatrix as $permission => $data) {
                $groupedMatrix[$user->id]['name'] = $user->name;
                $groupedMatrix[$user->id]['permissions'][$permission] = [
                    'description' => $data['description'],
                    'checked' => $data['users'][$user->id]
                ];
            }
        }

        return view('roles.permissions', compact('users', 'groupedMatrix', 'availablePermissions', 'roles'));
    }

    public function updateUserPermissions(Request $request)
    {
        $validated = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'array',
            'permissions.*.*' => 'boolean',
        ]);

        $users = User::all()->keyBy('id');

        foreach ($validated['permissions'] as $userId => $permissions) {
            if (isset($users[$userId])) {
                $grantedPermissions = array_keys(array_filter($permissions));
                $users[$userId]->syncPermissions($grantedPermissions);
            }
        }

        return back()->with('success', 'Permissions utilisateurs mises à jour avec succès.');
    }
}
