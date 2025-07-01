@extends('layouts.app')

@section('title', 'Présence du Jour - ANADEC RH')
@section('page-title', 'Présence du Jour')
@section('page-description', 'Suivi des présences pour le {{ $date->format("d/m/Y") }}')

@section('content')
<div class="space-y-6">
    <!-- Sélecteur de date -->
    <div class="bg-white p-6 rounded-lg shadow-sm border">
        <form method="GET" class="flex items-center space-x-4">
            <label for="date" class="text-sm font-medium text-gray-700">Date :</label>
            <input type="date" name="date" id="date" value="{{ $date->format('Y-m-d') }}" 
                   class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-anadec-blue focus:border-anadec-blue">
            <button type="submit" class="bg-anadec-blue text-white px-4 py-2 rounded-lg hover:bg-anadec-dark-blue">
                <i class="bx bx-calendar mr-2"></i>Afficher
            </button>
        </form>
    </div>

    <!-- Statistiques du jour -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                    <i class="bx bx-check-circle text-green-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $presences->where('statut', 'present')->count() }}</h3>
                    <p class="text-gray-600">Présents</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                    <i class="bx bx-time text-yellow-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $presences->where('statut', 'present_retard')->count() }}</h3>
                    <p class="text-gray-600">Retards</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                    <i class="bx bx-info-circle text-blue-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $presences->whereIn('statut', ['justifie', 'absence_autorisee'])->count() }}</h3>
                    <p class="text-gray-600">Justifiés</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                    <i class="bx bx-x-circle text-red-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $agentsAbsents->count() }}</h3>
                    <p class="text-gray-600">Non pointés</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Présences enregistrées -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Présences Enregistrées</h3>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @forelse($presences as $presence)
                <div class="px-6 py-4 border-b border-gray-100 last:border-b-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-anadec-blue rounded-full flex items-center justify-center">
                                <span class="text-xs font-medium text-white">
                                    {{ strtoupper(substr($presence->agent->prenoms, 0, 1) . substr($presence->agent->nom, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $presence->agent->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $presence->agent->direction }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center space-x-2">
                                <i class="bx {{ $presence->getStatutIcon() }} text-lg"></i>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $presence->getStatutBadgeClass() }}">
                                    {{ $presence->getStatutLabel() }}
                                </span>
                            </div>
                            @if($presence->heure_arrivee)
                                <p class="text-xs text-gray-500 mt-1">{{ $presence->heure_arrivee->format('H:i') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    <i class="bx bx-calendar-x text-4xl mb-2"></i>
                    <p>Aucune présence enregistrée pour cette date.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Agents non pointés -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Agents Non Pointés</h3>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @forelse($agentsAbsents as $agent)
                <div class="px-6 py-4 border-b border-gray-100 last:border-b-0">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center">
                                <span class="text-xs font-medium text-white">
                                    {{ strtoupper(substr($agent->prenoms, 0, 1) . substr($agent->nom, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $agent->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $agent->direction }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                Non pointé
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    <i class="bx bx-check-circle text-4xl mb-2 text-green-500"></i>
                    <p>Tous les agents ont été pointés !</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-white p-6 rounded-lg shadow-sm border">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Actions Rapides</h3>
        <div class="flex space-x-4">
            <a href="{{ route('presences.create') }}" 
               class="bg-anadec-blue text-white px-4 py-2 rounded-lg hover:bg-anadec-dark-blue flex items-center">
                <i class="bx bx-plus mr-2"></i>
                Nouvelle Présence
            </a>
            <a href="{{ route('presences.export', ['date' => $date->format('Y-m-d')]) }}" 
               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                <i class="bx bx-download mr-2"></i>
                Exporter le Jour
            </a>
        </div>
    </div>
</div>
@endsection