@extends('layouts.app')

@section('title', 'Permissions des Utilisateurs - ANADEC RH')
@section('page-title', 'Permissions des Utilisateurs')
@section('page-description', 'Attribuez des accès spécifiques à chaque utilisateur')

@section('content')
<div class="container mx-auto py-6">
    <div class="bg-white shadow rounded p-6">
        <h1 class="text-2xl font-bold mb-4">Détails du Rôle : {{ $role->display_name }}</h1>

        <p class="mb-4 text-gray-600">Description : {{ $role->description ?? 'Aucune description' }}</p>

        <div>
            <h2 class="font-semibold mb-2">Permissions assignées :</h2>
            <ul class="list-disc pl-5">
                @foreach($role->permissions as $permission)
                    <li>{{ $permission }}</li>
                @endforeach
            </ul>
        </div>

        <div class="mt-6">
            <h2 class="font-semibold mb-2">Utilisateurs ayant ce rôle :</h2>
            <ul class="list-disc pl-5">
                @forelse($role->users as $user)
                    <li>{{ $user->name }} ({{ $user->email }})</li>
                @empty
                    <li>Aucun utilisateur assigné</li>
                @endforelse
            </ul>
        </div>

        <a href="{{ route('roles.edit', $role) }}" class="mt-6 inline-block bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Modifier</a>
    </div>
</div>
@endsection
