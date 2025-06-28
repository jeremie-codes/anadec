@extends('layouts.app')

@section('title', 'Gestion des Permissions - ANADEC RH')
@section('page-title', 'Gestion des Permissions par Rôle')
@section('page-description', 'Configuration des permissions pour chaque rôle du système')

@section('content')
<div class="space-y-6">
    <!-- Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="text-blue-900 font-medium mb-1">Instructions</h4>
        <p class="text-blue-800 text-sm">
            Configurez les permissions pour chaque rôle. Les agents ayant un rôle spécifique hériteront automatiquement de ces permissions.
        </p>
    </div>

    <!-- Filtres -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="bx bx-filter mr-2 text-blue-600"></i>
                Filtres
            </h3>
        </div>
        <div class="p-6">
            <form method="GET" class="flex items-center space-x-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Rechercher un rôle..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="bx bx-search mr-2"></i>Filtrer
                </button>
            </form>
        </div>
    </div>

    <!-- Formulaire de permissions -->
    <form method="POST" action="{{ route('roles.update-permissions') }}">
        @csrf

        <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="bx bx-shield mr-2 text-indigo-600"></i>
                    Matrice des Permissions
                </h3>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="bx bx-save mr-2"></i>Sauvegarder
                </button>
            </div>

            <div class="p-6">
                @foreach($groupedPermissions as $category => $permissions)
                    <div class="mb-8">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 capitalize border-b border-gray-200 pb-2">
                            <i class="bx bx-folder mr-2 text-gray-600"></i>
                            {{ str_replace('-', ' ', $category) }}
                        </h4>

                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Permission</th>
                                        @foreach($roles as $role)
                                            <th class="px-4 py-3 text-center text-sm font-medium text-gray-700">
                                                <div class="flex flex-col items-center">
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $role->getBadgeClass() }}">
                                                        <i class="bx {{ $role->getIcon() }} mr-1"></i>
                                                        {{ $role->display_name }}
                                                    </span>
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($permissions as $permission => $description)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $description }}</div>
                                                    <div class="text-xs text-gray-500">{{ $permission }}</div>
                                                </div>
                                            </td>
                                            @foreach($roles as $role)
                                                <td class="px-4 py-3 text-center">
                                                    <label class="inline-flex items-center">
                                                        <input type="checkbox" 
                                                               name="permissions[{{ $role->id }}][{{ $permission }}]" 
                                                               value="1"
                                                               class="form-checkbox h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500"
                                                               {{ $role->hasPermission($permission) ? 'checked' : '' }}>
                                                    </label>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end">
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="bx bx-save mr-2"></i>Sauvegarder les Permissions
                </button>
            </div>
        </div>
    </form>

    <!-- Actions rapides -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-yellow-50 to-orange-50">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="bx bx-zap mr-2 text-yellow-600"></i>
                Actions Rapides
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button onclick="selectAllPermissions()" 
                        class="flex items-center justify-center p-4 bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl hover:from-green-100 hover:to-emerald-200 transition-all duration-200 border border-green-200">
                    <i class="bx bx-check-square text-green-600 text-2xl mr-3"></i>
                    <div class="text-left">
                        <p class="font-semibold text-green-900">Tout Sélectionner</p>
                        <p class="text-sm text-green-700">Toutes les permissions</p>
                    </div>
                </button>

                <button onclick="deselectAllPermissions()" 
                        class="flex items-center justify-center p-4 bg-gradient-to-br from-red-50 to-rose-100 rounded-xl hover:from-red-100 hover:to-rose-200 transition-all duration-200 border border-red-200">
                    <i class="bx bx-square text-red-600 text-2xl mr-3"></i>
                    <div class="text-left">
                        <p class="font-semibold text-red-900">Tout Désélectionner</p>
                        <p class="text-sm text-red-700">Aucune permission</p>
                    </div>
                </button>

                <a href="{{ route('roles.index') }}" 
                   class="flex items-center justify-center p-4 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl hover:from-blue-100 hover:to-indigo-200 transition-all duration-200 border border-blue-200">
                    <i class="bx bx-arrow-back text-blue-600 text-2xl mr-3"></i>
                    <div class="text-left">
                        <p class="font-semibold text-blue-900">Retour</p>
                        <p class="text-sm text-blue-700">Gestion des rôles</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function selectAllPermissions() {
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllPermissions() {
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Sélectionner/désélectionner toutes les permissions d'un rôle
function toggleRolePermissions(roleId) {
    const checkboxes = document.querySelectorAll(`input[name*="[${roleId}]"]`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });
}

// Sélectionner/désélectionner toutes les permissions d'une catégorie
function toggleCategoryPermissions(category) {
    const categorySection = document.querySelector(`[data-category="${category}"]`);
    if (categorySection) {
        const checkboxes = categorySection.querySelectorAll('input[type="checkbox"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
    }
}
</script>
@endsection