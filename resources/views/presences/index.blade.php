@extends('layouts.app')

@section('title', 'Gestion des Présences - ANADEC RH')
@section('page-title', 'Gestion des Présences')
@section('page-description', 'Suivi et gestion des présences')

@section('content')
<div class="space-y-6">
    <!-- Statistiques du jour -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <i class="bx bx-group text-blue-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                    <p class="text-sm text-gray-600">Total</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <i class="bx bx-check-circle text-green-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['presents']) }}</p>
                    <p class="text-sm text-gray-600">Présents</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                    <i class="bx bx-time text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['retards']) }}</p>
                    <p class="text-sm text-gray-600">Retards</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <i class="bx bx-info-circle text-blue-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['justifies']) }}</p>
                    <p class="text-sm text-gray-600">Justifiés</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                    <i class="bx bx-calendar-check text-purple-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['autorises']) }}</p>
                    <p class="text-sm text-gray-600">Autorisés</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                    <i class="bx bx-x-circle text-red-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['absents']) }}</p>
                    <p class="text-sm text-gray-600">Absents</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et actions -->
    <div class="bg-white p-3 rounded-lg shadow-sm border">
        <div class="flex space-x-2 mb-3">
            <a href="{{ route('presences.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                <i class="bx bx-download mr-2"></i>
                Exporter
            </a>
            <a href="{{ route('presences.create') }}"
                class="bg-anadec-blue text-white px-4 py-2 rounded-lg hover:bg-anadec-dark-blue flex items-center">
                <i class="bx bx-plus mr-2"></i>
                Nouvelle Présence
            </a>
        </div>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">

            <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
                <form method="GET" class="flex items-center space-x-2">
                    <!-- Date -->
                    <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}"
                           class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-anadec-blue focus:border-anadec-blue">

                    <!-- Recherche -->
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Rechercher..."
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-anadec-blue focus:border-anadec-blue">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <i class="bx bx-search text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Filtre par statut -->
                    <select name="statut" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-anadec-blue focus:border-anadec-blue">
                        <option value="">Tous les statuts</option>
                        <option value="present" {{ request('statut') == 'present' ? 'selected' : '' }}>Présents</option>
                        <option value="present_retard" {{ request('statut') == 'present_retard' ? 'selected' : '' }}>Présents avec retard</option>
                        <option value="justifie" {{ request('statut') == 'justifie' ? 'selected' : '' }}>Justifiés</option>
                        <option value="absence_autorisee" {{ request('statut') == 'absence_autorisee' ? 'selected' : '' }}>Absence autorisée</option>
                        <option value="absent" {{ request('statut') == 'absent' ? 'selected' : '' }}>Absents</option>
                    </select>

                    <!-- Filtre par direction -->
                    <select name="direction" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-anadec-blue focus:border-anadec-blue">
                        <option value="">Toutes les directions</option>
                        <option value="Direction Générale" {{ request('direction') == 'Direction Générale' ? 'selected' : '' }}>Direction Générale</option>
                        <option value="Direction RH" {{ request('direction') == 'Direction RH' ? 'selected' : '' }}>Direction RH</option>
                        <option value="Direction Financière" {{ request('direction') == 'Direction Financière' ? 'selected' : '' }}>Direction Financière</option>
                        <option value="Direction Technique" {{ request('direction') == 'Direction Technique' ? 'selected' : '' }}>Direction Technique</option>
                        <option value="Direction Administrative" {{ request('direction') == 'Direction Administrative' ? 'selected' : '' }}>Direction Administrative</option>
                        <option value="Direction Commerciale" {{ request('direction') == 'Direction Commerciale' ? 'selected' : '' }}>Direction Commerciale</option>
                    </select>

                    <button type="submit" class="bg-anadec-blue text-white px-4 py-2 rounded-lg hover:bg-anadec-dark-blue">
                        <i class="bx bx-filter-alt mr-1"></i> Filtrer
                    </button>

                    {{-- @if(request()->hasAny(['search', 'statut', 'direction']) || request('date') != date('Y-m-d')) --}}
                    @if(request()->hasAny(['search', 'statut', 'direction', 'date']))
                        <a href="{{ route('presences.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                            <i class="bx bx-x mr-1"></i> Effacer
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Tableau des présences -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Direction</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrivée</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Départ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($presences as $presence)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $presence->date->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <div class="h-8 w-8 rounded-full bg-anadec-blue flex items-center justify-center">
                                        <span class="text-xs font-medium text-white">
                                            {{ strtoupper(substr($presence->agent->prenoms, 0, 1) . substr($presence->agent->nom, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $presence->agent->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $presence->agent->matricule }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $presence->agent->direction }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $presence->heure_arrivee ? $presence->heure_arrivee->format('H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $presence->heure_depart ? $presence->heure_depart->format('H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="bx {{ $presence->getStatutIcon() }} mr-2 text-lg"></i>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $presence->getStatutBadgeClass() }}">
                                    {{ $presence->getStatutLabel() }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $presence->motif ?: '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('presences.edit', $presence) }}"
                               class="text-yellow-600 hover:text-yellow-800">
                                <i class="bx bx-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('presences.destroy', $presence) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-red-600 hover:text-red-800"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette présence ?')">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            Aucune présence trouvée pour cette période.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($presences->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200">
            {{ $presences->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
