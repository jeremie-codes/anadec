@extends('layouts.app')

@section('title', 'Permissions des Utilisateurs - ANADEC RH')
@section('page-title', 'Permissions des Utilisateurs')
@section('page-description', 'Attribuez des accès spécifiques à chaque utilisateur')

@section('content')
<div class="max-w-6xl mx-auto py-10 px-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Utilisateurs & Attribution des rôles</h2>

    <form method="GET" class="mb-6 flex space-x-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher..."
            class="border border-gray-300 rounded px-3 py-2 focus:outline-none">

        <select name="role" class="border border-gray-300 rounded px-3 py-2">
            <option value="">Tous les rôles</option>
            <option value="no_role" {{ request('role') === 'no_role' ? 'selected' : '' }}>Sans rôle</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>{{ $role->display_name }}</option>
            @endforeach
        </select>

        <button type="submit"
            class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
            Filtrer
        </button>
    </form>

    <table class="min-w-full bg-white border border-gray-300 text-sm">
        <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="py-2 px-4">Nom</th>
                <th class="py-2 px-4">Email</th>
                <th class="py-2 px-4">Rôle</th>
                <th class="py-2 px-4">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr class="border-t border-gray-200">
                    <td class="py-2 px-4">{{ $user->name }}</td>
                    <td class="py-2 px-4">{{ $user->email }}</td>
                    <td class="py-2 px-4">{{ $user->role?->display_name ?? 'Aucun' }}</td>
                    <td class="py-2 px-4">
                        <form method="POST" action="{{ route('roles.update-user-role', $user) }}" class="inline-flex items-center space-x-2">
                            @csrf
                            @method('PUT')

                            <select name="role_id" class="border border-gray-300 rounded px-2 py-1 text-sm">
                                <option value="">Aucun</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                        {{ $role->display_name }}
                                    </option>
                                @endforeach
                            </select>

                            <button type="submit"
                                class="px-2 py-1 bg-indigo-500 text-white text-sm rounded hover:bg-indigo-600">
                                Appliquer
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-6">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection
