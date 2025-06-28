{{-- @extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-10 px-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Modifier le rôle : {{ $role->display_name }}</h2>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('roles.update', $role) }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block font-medium text-gray-700">Nom du rôle</label>
            <input type="text" name="display_name" value="{{ old('display_name', $role->display_name) }}"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring focus:ring-indigo-200" required>
        </div>

        <div class="mb-4">
            <label class="block font-medium text-gray-700">Description</label>
            <textarea name="description"
                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring focus:ring-indigo-200"
                rows="3">{{ old('description', $role->description) }}</textarea>
        </div>

        <div class="mb-6">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" class="form-checkbox" {{ $role->is_active ? 'checked' : '' }}>
                <span class="ml-2 text-gray-700">Rôle actif</span>
            </label>
        </div>

        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Permissions disponibles</h3>

            @foreach ($groupedPermissions as $category => $permissions)
                <div class="mb-6">
                    <h4 class="text-md font-semibold text-indigo-700 mb-2 capitalize">{{ str_replace('-', ' ', $category) }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach ($permissions as $key => $label)
                            <label class="inline-flex items-center space-x-2">
                                <input type="checkbox" name="permissions[]" value="{{ $key }}"
                                    class="form-checkbox text-indigo-600"
                                    {{ in_array($key, $role->permissions ?? []) ? 'checked' : '' }}>
                                <span class="text-gray-700">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('roles.index') }}"
                class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 transition">
                Annuler
            </a>
            <button type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection --}}

@section('content')
<div class="container mx-auto py-6">
    <div class="bg-white shadow rounded p-6">
        <h1 class="text-2xl font-bold mb-4">Modifier le Rôle : {{ $role->display_name }}</h1>

        <form action="{{ route('roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block font-semibold mb-1">Nom affiché</label>
                <input type="text" name="display_name" class="w-full border rounded p-2" value="{{ old('display_name', $role->display_name) }}">
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full border rounded p-2">{{ old('description', $role->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block font-semibold mb-2">Activer le rôle</label>
                <input type="checkbox" name="is_active" value="1" {{ $role->is_active ? 'checked' : '' }}>
            </div>

            <div class="mb-6">
                <h2 class="font-bold text-lg mb-2">Permissions disponibles</h2>
                @foreach($groupedPermissions as $category => $permissions)
                    <div class="mb-4">
                        <h3 class="text-md font-semibold mb-1 uppercase">{{ ucfirst($category) }}</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                            @foreach($permissions as $key => $label)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="permissions[]" value="{{ $key }}" {{ in_array($key, $role->permissions ?? []) ? 'checked' : '' }} class="mr-2">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">Mettre à jour</button>
        </form>
    </div>
</div>
@endsection
