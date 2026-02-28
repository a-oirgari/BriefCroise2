@extends('layouts.app')
@section('title', 'Modifier la colocation')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="mb-6">
        <a href="{{ route('colocations.show', $colocation->id) }}" class="text-sm text-gray-500 hover:text-indigo-600">← Retour</a>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Modifier la colocation</h1>
        <form method="POST" action="{{ route('colocations.update', $colocation->id) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom de la colocation</label>
                <input type="text" name="name" value="{{ old('name', $colocation->name) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
            </div>
            <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors">
                Enregistrer
            </button>
        </form>
    </div>
</div>
@endsection