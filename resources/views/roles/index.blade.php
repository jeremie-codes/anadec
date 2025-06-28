@extends('layouts.app')

@section('title', 'Gestion des Rôles et Comptes - ANADEC RH')
@section('page-title', 'Gestion des Rôles et Comptes Utilisateurs')
@section('page-description', 'Attribution des rôles et création des comptes utilisateurs pour les agents')

@section('content')
<div class="space-y-6">
    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 overflow-hidden shadow-lg rounded-xl border border-blue-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="bx bx-group text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-blue-100">Total Agents</p>
                        <p class="text-3xl font-bold text-white">{{ number_format($stats['total_agents']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-emerald-600 overflow-hidden shadow-lg rounded-xl border border-green-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="bx bx-user-check text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-100">Avec Compte</p>
                        <p class="text-3xl font-bold text-white">{{ number_format($stats['with_accounts']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-red-600 overflow-hidden shadow-lg rounded-xl border border-orange-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="bx bx-user-x text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-orange-100">Sans Compte</p>
                        <p class="text-3xl font-bold text-white">{{ number_format($stats['without_accounts']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-pink-600 overflow-hidden shadow-lg rounded-xl border border-purple-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="bx bx-user-voice text-white text-2xl"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-purple-100">Utilisateurs Actifs</p>
                        <p class="text-3xl font-bold text-white">{{ number_format($stats['active_users']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="bx bx-zap mr-2 text-indigo-600"></i>
                    Actions Rapides
                </h3>
                <a href="{{ route('roles.permissions') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="bx bx-shield mr-2"></i>Gérer les Permissions
                </a>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($roles as $role)
                    <a href="{{ route('roles.agents-by-role', $role) }}" 
                       class="group flex items-center p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl hover:from-gray-100 hover:to-gray-200 transition-all duration-200 border border-gray-200">
                        <i class="bx {{ $role->getIcon() }} text-3xl mr-3 group-hover:scale-110 transition-transform" style="color: {{ $role->getBadgeClass() === 'bg-red-100 text-red-800' ? '#dc2626' : '#6b7280' }}"></i>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $role->display_name }}</p>
                            <p class="text-sm text-gray-600">{{ $role->agents_count ?? 0 }} agents</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="bx bx-filter mr-2 text-blue-600"></i>
                Filtres et Recherche
            </h3>
        </div>
        <div class="p-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Nom, prénom, matricule..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rôle</label>
                    <select name="role_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous les rôles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Statut Compte</label>
                    <select name="user_status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Tous</option>
                        <option value="with_account" {{ request('user_status') === 'with_account' ? 'selected' : '' }}>Avec compte</option>
                        <option value="without_account" {{ request('user_status') === 'without_account' ? 'selected' : '' }}>Sans compte</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="bx bx-search mr-2"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des agents -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="bx bx-list-ul mr-2 text-gray-600"></i>
                Liste des Agents ({{ $agents->total() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle Actuel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compte Utilisateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($agents as $agent)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($agent->hasPhoto())
                                        <img src="{{ $agent->photo_url }}" alt="{{ $agent->full_name }}" 
                                             class="w-10 h-10 rounded-full object-cover mr-3">
                                    @else
                                        <div class="w-10 h-10 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-sm font-bold text-white">{{ $agent->initials }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $agent->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $agent->matricule }} • {{ $agent->direction }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('roles.update-agent-role', $agent) }}" class="flex items-center space-x-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="role_id" class="border border-gray-300 rounded px-2 py-1 text-sm" onchange="this.form.submit()">
                                        <option value="">Aucun rôle</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ $agent->role_id == $role->id ? 'selected' : '' }}>
                                                {{ $role->display_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                                @if($agent->role)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $agent->role->getBadgeClass() }} mt-1">
                                        <i class="bx {{ $agent->role->getIcon() }} mr-1"></i>
                                        {{ $agent->role->display_name }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($agent->user)
                                    <div class="flex items-center">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="bx bx-check-circle mr-1"></i>
                                            Actif
                                        </span>
                                        <div class="ml-2 text-sm text-gray-600">{{ $agent->user->email }}</div>
                                    </div>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="bx bx-x-circle mr-1"></i>
                                        Aucun compte
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @if(!$agent->user)
                                        <!-- Créer un compte -->
                                        <button onclick="openCreateAccountModal({{ $agent->id }}, '{{ $agent->full_name }}')" 
                                                class="text-green-600 hover:text-green-900 bg-green-100 hover:bg-green-200 px-2 py-1 rounded transition-colors">
                                            <i class="bx bx-user-plus"></i>
                                        </button>
                                    @else
                                        <!-- Réinitialiser le mot de passe -->
                                        <form method="POST" action="{{ route('roles.reset-password', $agent) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('Réinitialiser le mot de passe de {{ $agent->full_name }} ?')"
                                                    class="text-blue-600 hover:text-blue-900 bg-blue-100 hover:bg-blue-200 px-2 py-1 rounded transition-colors">
                                                <i class="bx bx-key"></i>
                                            </button>
                                        </form>

                                        <!-- Supprimer le compte -->
                                        <form method="POST" action="{{ route('roles.delete-user-account', $agent) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return confirm('Supprimer le compte utilisateur de {{ $agent->full_name }} ?')"
                                                    class="text-red-600 hover:text-red-900 bg-red-100 hover:bg-red-200 px-2 py-1 rounded transition-colors">
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Voir l'agent -->
                                    <a href="{{ route('agents.show', $agent) }}" 
                                       class="text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-2 py-1 rounded transition-colors">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="bx bx-user-x text-4xl mb-2"></i>
                                    <p>Aucun agent trouvé.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($agents->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $agents->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal de création de compte -->
<div id="createAccountModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Créer un compte utilisateur</h3>
            <form id="createAccountForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Agent</label>
                    <input type="text" id="agentName" readonly class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe (optionnel)</label>
                    <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Si vide, le mot de passe par défaut sera "password123"</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCreateAccountModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Créer le compte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCreateAccountModal(agentId, agentName) {
    document.getElementById('agentName').value = agentName;
    document.getElementById('createAccountForm').action = `/roles/agents/${agentId}/create-user-account`;
    document.getElementById('createAccountModal').classList.remove('hidden');
}

function closeCreateAccountModal() {
    document.getElementById('createAccountModal').classList.add('hidden');
}

// Fermer le modal en cliquant à l'extérieur
document.getElementById('createAccountModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCreateAccountModal();
    }
});
</script>
@endsection