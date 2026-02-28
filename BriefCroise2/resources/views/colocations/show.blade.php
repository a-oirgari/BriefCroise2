@extends('layouts.app')
@section('title', $colocation->name)

@section('content')
@php
    $balanceService = app(\App\Services\BalanceService::class);
    $isOwner = $membership->role === 'owner';
@endphp

<div class="space-y-6">

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-xs font-medium text-indigo-500 uppercase tracking-wide">Colocation</p>
                <h1 class="text-2xl font-bold text-gray-800 mt-0.5">{{ $colocation->name }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ $activeMembers->count() }} membre(s) actif(s)</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if($isOwner)
                    
                    <button onclick="document.getElementById('modal-invite').classList.remove('hidden')"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                        + Inviter
                    </button>
                    <a href="{{ route('colocations.edit', $colocation->id) }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Modifier
                    </a>
                    <a href="{{ route('categories.index', $colocation->id) }}"
                       class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Catégories
                    </a>
                    <form method="POST" action="{{ route('colocations.cancel', $colocation->id) }}"
                          onsubmit="return confirm('Annuler la colocation ? Cette action est irréversible.')">
                        @csrf
                        <button type="submit"
                                class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Annuler
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('colocations.leave', $colocation->id) }}"
                          onsubmit="return confirm('Quitter la colocation ?')">
                        @csrf
                        <button type="submit"
                                class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Quitter
                        </button>
                    </form>
                @endif
                <a href="{{ route('payments.index', $colocation->id) }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                     Soldes
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="text-base font-semibold text-gray-800 mb-4">Membres</h2>
            <div class="space-y-3">
                @foreach($activeMembers as $m)
                @php
                    $balance = $balanceService->getUserBalance($colocation, $m->user_id);
                @endphp
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr($m->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $m->user->name }}
                                @if($m->user_id === $user->id) <span class="text-xs text-gray-400">(vous)</span> @endif
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $m->role === 'owner' ? ' Owner' : ' Membre' }}
                                · Rep: {{ $m->user->reputation >= 0 ? '+' : '' }}{{ $m->user->reputation }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold {{ $balance >= 0 ? 'text-green-600' : 'text-red-500' }}">
                            {{ $balance >= 0 ? '+' : '' }}{{ number_format($balance, 2) }}dh
                        </span>
                        @if($isOwner && $m->user_id !== $user->id)
                            <form method="POST"
                                  action="{{ route('colocations.members.remove', [$colocation->id, $m->user_id]) }}"
                                  onsubmit="return confirm('Retirer ce membre ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-400 hover:text-red-600">✕</button>
                            </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

       
        <div class="lg:col-span-2 space-y-4">

            
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-base font-semibold text-gray-800 mb-4">Ajouter une dépense</h2>
                <form method="POST" action="{{ route('expenses.store', $colocation->id) }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <input type="text" name="title" placeholder="Titre" required value="{{ old('title') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                        </div>
                        <div>
                            <input type="number" name="amount" placeholder="Montant (dh)" step="0.01" min="0.01" required
                                   value="{{ old('amount') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                        </div>
                        <div>
                            <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                        </div>
                        <div>
                            <select name="payer_id" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">Payeur</option>
                                @foreach($activeMembers as $m)
                                    <option value="{{ $m->user_id }}" {{ old('payer_id', $user->id) == $m->user_id ? 'selected' : '' }}>
                                        {{ $m->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="category_id"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                <option value="">Catégorie (optionnel)</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit"
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg text-sm font-semibold transition-colors">
                        Ajouter la dépense
                    </button>
                </form>
            </div>

            
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-semibold text-gray-800">Dépenses</h2>
                    
                    <form method="GET" action="{{ route('colocations.show', $colocation->id) }}">
                        <select name="month" onchange="this.form.submit()"
                                class="border border-gray-300 rounded-lg px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <option value="">Tous les mois</option>
                            @foreach($availableMonths as $month)
                                <option value="{{ $month }}" {{ $selectedMonth == $month ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->isoFormat('MMMM YYYY') }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                @if($expenses->isEmpty())
                    <p class="text-sm text-gray-400 text-center py-6">Aucune dépense enregistrée.</p>
                @else
                    <div class="space-y-2">
                        @foreach($expenses as $expense)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold mt-0.5">
                                    {{ strtoupper(substr($expense->payer->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $expense->title }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $expense->payer->name }} · {{ $expense->date->format('d/m/Y') }}
                                        @if($expense->category) · <span class="text-indigo-500">{{ $expense->category->name }}</span> @endif
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold text-gray-800">{{ number_format($expense->amount, 2) }} dh</span>
                                @if($expense->payer_id === $user->id || $isOwner)
                                    <form method="POST"
                                          action="{{ route('expenses.destroy', [$colocation->id, $expense->id]) }}"
                                          onsubmit="return confirm('Supprimer cette dépense ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-300 hover:text-red-500 transition-colors text-xs">✕</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between">
                        <span class="text-sm font-medium text-gray-600">Total</span>
                        <span class="text-sm font-bold text-gray-800">{{ number_format($expenses->sum('amount'), 2) }} dh</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


@if($isOwner)
<div id="modal-invite" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Inviter un membre</h3>
            <button onclick="document.getElementById('modal-invite').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
        </div>
        <form method="POST" action="{{ route('invitations.store', $colocation->id) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email de la personne à inviter</label>
                <input type="email" name="email" required placeholder="exemple@email.com"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
            </div>
            <div class="flex gap-3">
                <button type="button"
                        onclick="document.getElementById('modal-invite').classList.add('hidden')"
                        class="flex-1 border border-gray-300 text-gray-700 py-2 rounded-lg text-sm font-medium">
                    Annuler
                </button>
                <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg text-sm font-semibold transition-colors">
                    Envoyer l'invitation
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection