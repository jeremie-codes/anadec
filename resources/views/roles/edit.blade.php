@extends('layouts.app')

@section('title', 'Modifier le Rôle - ANADEC RH')
@section('page-title', 'Modifier le Rôle : ' . $role->display_name)
@section('page-description', 'Modification des informations et permissions du rôle')

@section('content')
<div class="space-y-6">
    <!-- Formulaire de modification -->
    <form method="POST" action="{{ route('roles.update', $role) }}">
        @csrf
        @method('PUT')

        <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="bx bx-edit mr-2 text-indigo-600"></i>
                        Informations du Rôle
                    </h3>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('roles.show', $role) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="bx bx-arrow-back mr-2"></i>Retour
                        </a>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="bx bx-save mr-2"></i>Sauvegarder
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom d'affichage *</label>
                        <input type="text" name="display_name" value="{{ old('display_name', $role->display_name) }}" 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               required>
                        @error('display_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_active" value="1" 
                                   class="form-checkbox h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500"
                                   {{ old('is_active', $role->is_active) ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">Rôle actif</span>
                        </label>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="3" 
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $role->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions -->
        <div class="bg-white shadow-lg rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="bx bx-shield mr-2 text-green-600"></i>
                    Permissions du Rôle
                </h3>
            </div>

            <div class="p-6">
                @foreach($groupedPermissions as $category => $permissions)
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-800 capitalize border-b border-gray-200 pb-2">
                                <i class="bx bx-folder mr-2 text-gray-600"></i>
                                {{ str_replace('-', ' ', $category) }}
                            </h4>
                            <button type="button" onclick="toggleCategoryPermissions('{{ $category }}')" 
                                    class="text-sm text-blue-600 hover:text-blue-800">
                                Tout sélectionner/désélectionner
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" data-category="{{ $category }}">
                            @foreach($permissions as $permission => $description)
                                <label class="inline-flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission }}" 
                                           class="form-checkbox h-5 w-5 text-indigo-600 rounded focus:ring-indigo-500 mt-0.5"
                                           {{ in_array($permission, $role->permissions ?? []) ? 'checked' : '' }}>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900">{{ $description }}</div>
                                        <div class="text-xs text-gray-500">{{ $permission }}</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                <div class="flex space-x-4">
                    <button type="button" onclick="selectAllPermissions()" 
                            class="text-sm text-green-600 hover:text-green-800">
                        <i class="bx bx-check-square mr-1"></i>Tout sélectionner
                    </button>
                    <button type="button" onclick="deselectAllPermissions()" 
                            class="text-sm text-red-600 hover:text-red-800">
                        <i class="bx bx-square mr-1"></i>Tout désélectionner
                    </button>
                </div>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="bx bx-save mr-2"></i>Sauvegarder les Modifications
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function selectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllPermissions() {
    document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function toggleCategoryPermissions(category) {
    const categorySection = document.querySelector(`[data-category="${category}"]`);
    if (categorySection) {
        const checkboxes = categorySection.querySelectorAll('input[name="permissions[]"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
    }
}
</script>
@endsection