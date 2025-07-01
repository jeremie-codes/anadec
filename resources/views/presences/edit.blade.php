@extends('layouts.app')

@section('title', 'Modifier Présence - ANADEC RH')
@section('page-title', 'Modifier Présence')
@section('page-description', 'Modification de la présence')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Modification de Présence</h3>
            <p class="text-sm text-gray-600">Modifiez les informations de présence pour {{ $presence->agent->full_name }}</p>
        </div>

        <form method="POST" action="{{ route('presences.update', $presence) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="agent_id" class="block text-sm font-medium text-gray-700">Agent *</label>
                    <select name="agent_id" id="agent_id" required
                            class="mt-1 py-2 px-4 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-anadec-blue focus:border-anadec-blue">
                        <option value="">Sélectionnez un agent...</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ old('agent_id', $presence->agent_id) == $agent->id ? 'selected' : '' }}>
                                {{ $agent->full_name }} ({{ $agent->matricule }}) - {{ $agent->direction }}
                            </option>
                        @endforeach
                    </select>
                    @error('agent_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700">Date *</label>
                    <input type="date" name="date" id="date" required
                           value="{{ old('date', $presence->date->format('Y-m-d')) }}"
                           class="mt-1 py-2 px-4 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-anadec-blue focus:border-anadec-blue">
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="statut" class="block text-sm font-medium text-gray-700">Statut *</label>
                    <select name="statut" id="statut" required
                            class="mt-1 py-2 px-4 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-anadec-blue focus:border-anadec-blue"
                            onchange="toggleTimeFields()">
                        <option value="">Sélectionnez...</option>
                        <option value="present" {{ old('statut', $presence->statut) == 'present' ? 'selected' : '' }}>Présent</option>
                        <option value="present_retard" {{ old('statut', $presence->statut) == 'present_retard' ? 'selected' : '' }}>Présent avec retard</option>
                        <option value="justifie" {{ old('statut', $presence->statut) == 'justifie' ? 'selected' : '' }}>Absence justifiée</option>
                        <option value="absence_autorisee" {{ old('statut', $presence->statut) == 'absence_autorisee' ? 'selected' : '' }}>Absence autorisée</option>
                        <option value="absent" {{ old('statut', $presence->statut) == 'absent' ? 'selected' : '' }}>Absent</option>
                    </select>
                    @error('statut')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="time-fields" class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="heure_arrivee" class="block text-sm font-medium text-gray-700">Heure d'Arrivée</label>
                        <input type="time" name="heure_arrivee" id="heure_arrivee"
                               value="{{ old('heure_arrivee', $presence->heure_arrivee ? $presence->heure_arrivee->format('H:i') : '') }}"
                               class="mt-1 py-2 px-4 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-anadec-blue focus:border-anadec-blue">
                        @error('heure_arrivee')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="heure_depart" class="block text-sm font-medium text-gray-700">Heure de Départ</label>
                        <input type="time" name="heure_depart" id="heure_depart"
                               value="{{ old('heure_depart', $presence->heure_depart ? $presence->heure_depart->format('H:i') : '') }}"
                               class="mt-1 py-2 px-4 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-anadec-blue focus:border-anadec-blue">
                        @error('heure_depart')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label for="motif" class="block text-sm font-medium text-gray-700">Motif</label>
                    <textarea name="motif" id="motif" rows="3"
                              class="mt-1 py-2 px-4 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-anadec-blue focus:border-anadec-blue"
                              placeholder="Motif de l'absence ou commentaire...">{{ old('motif', $presence->motif) }}</textarea>
                    @error('motif')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('presences.index') }}"
                   class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400">
                    Annuler
                </a>
                <button type="submit"
                        class="bg-anadec-blue text-white px-6 py-2 rounded-md hover:bg-anadec-dark-blue">
                    <i class="bx bx-save mr-2"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleTimeFields() {
    const statut = document.getElementById('statut').value;
    const timeFields = document.getElementById('time-fields');

    if (statut === 'present' || statut === 'present_retard') {
        timeFields.style.display = 'block';
    } else {
        timeFields.style.display = 'none';
    }
}

// Initialiser l'affichage au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    toggleTimeFields();
});
</script>
@endsection
