@extends('layouts.app')

@section('title', 'Détails du Rôle - ANADEC RH')
@section('page-title', 'Détails du Rôle : ' . $role->display_name)
@section('page-description', 'Informations détaillées sur le rôle et ses permissions')

@section('content')
<div class="space-y-6">
    <!-- Informations du rôle -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="bx {{ $role->getIcon() }} mr-2 text-indigo-600"></i>
                    Informations du Rôle
                </h3>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $role->getBadgeClass() }}">
                        {{ $role->display_name }}
                    </span>
                    <a href="{{ route('roles.edit', $role) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="bx bx-edit mr-2"></i>Modifier
                    </a>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Nom du Rôle</h4>
                    <p class="text-lg font-semibold text-gray-900">{{ $role->display_name }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Nom Système</h4>
                    <p class="text-lg text-gray-700 font-mono">{{ $role->name }}</p>
                </div>
                <div class="md:col-span-2">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Description</h4>
                    <p class="text-gray-700">{{ $role->description ?: 'Aucune description disponible.' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Statut</h4>
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $role->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        <i class="bx {{ $role->is_active ? 'bx-check-circle' : 'bx-x-circle' }} mr-1"></i>
                        {{ $role->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Nombre d'Agents</h4>
                    <p class="text-lg font-semibold text-gray-900">{{ $role->agents->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions du rôle -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="bx bx-shield mr-2 text-green-600"></i>
                Permissions Accordées ({{ count($role->permissions ?? []) }})
            </h3>
        </div>
        <div class="p-6">
            @if(!empty($role->permissions))
                @php
                    $groupedPermissions = [];
                    foreach ($role->permissions as $permission) {
                        $category = explode('.', $permission)[0];
                        $groupedPermissions[$category][] = $permission;
                    }
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($groupedPermissions as $category => $permissions)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-3 border-b border-gray-200 pb-2">
                                {{ str_replace('-', ' ', $category) }}
                            </h4>
                            <ul class="space-y-2">
                                @foreach($permissions as $permission)
                                    <li class="flex items-center text-sm text-gray-600">
                                        <i class="bx bx-check text-green-500 mr-2"></i>
                                        {{ $availablePermissions[$permission] ?? $permission }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="bx bx-shield-x text-4xl text-gray-300 mb-2"></i>
                    <p class="text-gray-500">Aucune permission accordée à ce rôle.</p>
                    <a href="{{ route('roles.edit', $role) }}" class="mt-2 inline-block text-blue-600 hover:text-blue-800">
                        <i class="bx bx-plus-circle mr-1"></i> Ajouter des permissions
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Agents ayant ce rôle -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="bx bx-group mr-2 text-blue-600"></i>
                Agents ayant ce Rôle ({{ $role->agents->count() }})
            </h3>
        </div>
        <div class="p-6">
            @if($role->agents->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($role->agents as $agent)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center">
                                @if($agent->hasPhoto())
                                    <img src="{{ $agent->photo_url }}" alt="{{ $agent->full_name }}" 
                                         class="w-12 h-12 rounded-full object-cover mr-3">
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-gray-400 to-gray-600 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-sm font-bold text-white">{{ $agent->initials }}</span>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $agent->full_name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $agent->matricule }}</p>
                                    <p class="text-xs text-gray-500">{{ $agent->direction }}</p>
                                    @if($agent->user)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mt-1">
                                            <i class="bx bx-user-check mr-1"></i>
                                            Compte actif
                                        </span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 mt-1">
                                            <i class="bx bx-user-x mr-1"></i>
                                            Sans compte
                                        </span>
                                    @endif
                                </div>
                                <a href="{{ route('agents.show', $agent) }}" 
                                   class="text-gray-400 hover:text-gray-600 transition-colors">
                                    <i class="bx bx-show"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="bx bx-user-x text-4xl text-gray-300 mb-2"></i>
                    <p class="text-gray-500">Aucun agent n'a ce rôle pour le moment.</p>
                    <a href="{{ route('roles.index') }}" class="mt-2 inline-block text-blue-600 hover:text-blue-800">
                        <i class="bx bx-arrow-back mr-1"></i> Retour à la gestion des rôles
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <i class="bx bx-cog mr-2 text-gray-600"></i>
                Actions
            </h3>
        </div>
        <div class="p-6">
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('roles.edit', $role) }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="bx bx-edit mr-2"></i>Modifier le Rôle
                </a>
                
                <a href="{{ route('roles.permissions') }}" 
                   class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="bx bx-shield mr-2"></i>Gérer les Permissions
                </a>
                
                <a href="{{ route('roles.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="bx bx-arrow-back mr-2"></i>Retour à la Liste
                </a>
            </div>
        </div>
    </div>
</div>
@endsection