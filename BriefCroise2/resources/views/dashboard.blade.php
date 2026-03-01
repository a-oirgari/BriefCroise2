@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Bonjour, {{ $user->name }} </h1>
        <p class="text-gray-500 text-sm mt-1">Bienvenue sur EasyColoc</p>
    </div>

    @if($colocation)
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-indigo-500 uppercase tracking-wide mb-1">Votre colocation</p>
                    <h2 class="text-xl font-bold text-gray-800">{{ $colocation->name }}</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $colocation->activeMembers()->count() }} membre(s) actif(s)
                    </p>
                </div>
                <a href="{{ route('colocations.show', $colocation->id) }}"
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    Voir la colocation
                </a>
            </div>
        </div>

        
        @php
            $balanceService = app(\App\Services\BalanceService::class);
            $userBalance = $balanceService->getUserBalance($colocation, $user->id);
            $totalExpenses = $colocation->expenses()->sum('amount');
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-xs text-gray-500 mb-1">Total dépenses</p>
                <p class="text-xl font-bold text-gray-800">{{ number_format($totalExpenses, 2) }} dh</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-xs text-gray-500 mb-1">Votre solde</p>
                <p class="text-xl font-bold {{ $userBalance >= 0 ? 'text-green-600' : 'text-red-500' }}">
                    {{ $userBalance >= 0 ? '+' : '' }}{{ number_format($userBalance, 2) }} dh
                </p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-xs text-gray-500 mb-1">Votre réputation</p>
                <p class="text-xl font-bold {{ $user->reputation >= 0 ? 'text-indigo-600' : 'text-red-500' }}">
                    {{ $user->reputation >= 0 ? '+' : '' }}{{ $user->reputation }} ⭐
                </p>
            </div>
        </div>

    @else
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-10 text-center">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-800 mb-2">Vous n'avez pas encore de colocation</h2>
            <p class="text-gray-500 text-sm mb-6">Créez une colocation ou rejoignez-en une via un lien d'invitation.</p>
            <a href="{{ route('colocations.create') }}"
               class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg text-sm font-semibold transition-colors">
                Créer une colocation
            </a>
        </div>
    @endif
</div>
@endsection