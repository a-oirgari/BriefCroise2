@extends('layouts.auth')
@section('title', 'Invitation invalide')

@section('content')
<div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 text-center">
    <div class="text-5xl mb-4">⚠️</div>
    <h2 class="text-xl font-bold text-gray-800 mb-2">Invitation invalide</h2>
    <p class="text-gray-500 text-sm mb-6">{{ $message ?? 'Cette invitation est expirée ou déjà utilisée.' }}</p>
    <a href="{{ route('login') }}"
       class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
        Retour à l'accueil
    </a>
</div>
@endsection