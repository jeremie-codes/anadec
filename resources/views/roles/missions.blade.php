@extends('layouts.app')

@section('title', 'Permissions des Utilisateurs - ANADEC RH')
@section('page-title', 'Permissions des Utilisateurs')
@section('page-description', 'Attribuez des accès spécifiques à chaque utilisateur')

@section('content')
<div class="space-y-6">
    <!-- Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h4 class="text-blue-900 font-medium mb-1">Instructions</h4>
        <p class="text-blue-800 text-sm">
            Sélectionnez les interfaces et actions spécifiques auxquelles chaque utilisateur a accès, en plus de celles données par son rôle.
        </p>
    </div>

    <!-- Formulaire -->
    <form method="POST" action="{{ route('roles.update-permissions') }}" id="user-permissions-form">
        @csrf

        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="bx bx-user-check mr-2 text-indigo-600"></i>
                    Permissions personnalisées des utilisateurs
                </h3>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                    <i class="bx bx-save mr-2"></i>Sauvegarder
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">Utilisateur</th>
                            @foreach($availablePermissions as $permission => $description)
                                <th class="px-6 py-3 text-center font-medium text-gray-500 uppercase">
                                    {{ $permission }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($users as $user)
                            <tr>
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <div>{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                </td>
                                @foreach($availablePermissions as $permission => $desc)
                                    <td class="px-6 py-4 text-center">
                                        <input type="checkbox"
                                               name="permissions[{{ $user->id }}][{{ $permission }}]"
                                               class="form-checkbox h-5 w-5 text-indigo-600 rounded"
                                               {{ $groupedMatrix[$user->id]['permissions'][$permission]['checked'] ?? false ? 'checked' : '' }}>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
@endsection
