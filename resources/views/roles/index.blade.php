{{-- @extends('layouts.app')

@section('title', 'Gestion des Rôles - ANADEC RH')
@section('page-title', 'Gestion des Rôles')
@section('page-description', 'Administration des rôles et permissions du système')
 --}}

@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Liste des rôles</h2>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <table class="min-w-full bg-white border border-gray-300 rounded shadow">
        <thead>
            <tr class="bg-gray-100 text-left text-sm font-medium text-gray-700">
                <th class="py-3 px-4">Nom</th>
                <th class="py-3 px-4">Description</th>
                <th class="py-3 px-4">Utilisateurs</th>
                <th class="py-3 px-4">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $role)
                <tr class="border-t border-gray-200 text-sm">
                    <td class="py-2 px-4 font-semibold">{{ $role->display_name }}</td>
                    <td class="py-2 px-4">{{ $role->description }}</td>
                    <td class="py-2 px-4">{{ $role->users_count }}</td>
                    <td class="py-2 px-4 space-x-2">
                        <a href="{{ route('roles.edit', $role) }}" class="text-blue-600 hover:underline">Modifier</a>
                        <a href="{{ route('roles.show', $role) }}" class="text-gray-700 hover:underline">Détails</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
