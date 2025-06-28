@extends('layouts.app')

@section('title', 'Agents par Rôle - ANADEC RH')
@section('page-title', 'Agents ayant le rôle : ' . $role->display_name)
@section('page-description', 'Liste des agents ayant ce rôle spécifique')

@section('content')
<div class="space-y-6">
    <!-- Informations du rôle -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="bx {{ $role->getIcon() }} mr-3 text-indigo-600 text-2xl"></i>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $role->display_name }}</h3>
                        <p class="text-sm text-gray-600">{{ $role->description }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $role->getBadgeClass() }}">
                        {{ $agents->total() }} agents
                    </span>
                    <a href="{{ route('roles.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="bx bx-arrow-back mr-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des agents -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="bx bx-group mr-2 text-gray-600"></i>
                Agents ({{ $agents->total() }})
            </h3>
        </div>

        @if($agents->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Direction/Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compte Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($agents as $agent)
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
                                            <div class="text-sm text-gray-500">{{ $agent->matricule }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $agent->direction }}</div>
                                    <div class="text-sm text-gray-500">{{ $agent->service }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $agent->getStatutBadgeClass() }}">
                                        {{ $agent->getStatutLabel() }}
                                    </span>
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
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($agents->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $agents->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <i class="bx bx-user-x text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun agent trouvé</h3>
                <p class="text-gray-500">Aucun agent n'a encore été assigné à ce rôle.</p>
                <a href="{{ route('roles.index') }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                    <i class="bx bx-arrow-back mr-1"></i> Retour à la gestion des rôles
                </a>
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