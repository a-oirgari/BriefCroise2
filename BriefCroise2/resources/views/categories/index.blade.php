@extends('layouts.app')
@section('title', 'Catégories')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <a href="{{ route('colocations.show', $colocation->id) }}" class="text-sm text-gray-500 hover:text-indigo-600">← Retour</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-2">Gérer les catégories</h1>
        <p class="text-sm text-gray-500">{{ $colocation->name }}</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Nouvelle catégorie</h2>
        <form method="POST" action="{{ route('categories.store', $colocation->id) }}" class="flex gap-3">
            @csrf
            <input type="text" name="name" placeholder="Nom de la catégorie" required
                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors">
                Créer
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Catégories existantes</h2>
        @if($categories->isEmpty())
            <p class="text-sm text-gray-400 text-center py-6">Aucune catégorie créée.</p>
        @else
            <div class="space-y-2">
                @foreach($categories as $cat)
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $cat->name }}</p>
                        <p class="text-xs text-gray-400">{{ $cat->expenses_count }} dépense(s)</p>
                    </div>
                    <form method="POST"
                          action="{{ route('categories.destroy', [$colocation->id, $cat->id]) }}"
                          onsubmit="return confirm('Supprimer cette catégorie ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-sm text-red-400 hover:text-red-600 transition-colors">Supprimer</button>
                    </form>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection