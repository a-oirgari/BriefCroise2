@extends('layouts.app')
@section('title', 'Soldes & Remboursements')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('colocations.show', $colocation->id) }}" class="text-sm text-gray-500 hover:text-indigo-600">← Retour</a>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">Soldes & Remboursements</h1>
            <p class="text-sm text-gray-500">{{ $colocation->name }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Soldes individuels</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($members as $m)
            @php $balance = $balances[$m->user_id] ?? 0; @endphp
            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">
                        {{ strtoupper(substr($m->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $m->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $m->role === 'owner' ? ' Owner' : ' Membre' }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-base font-bold {{ $balance >= 0 ? 'text-green-600' : 'text-red-500' }}">
                        {{ $balance >= 0 ? '+' : '' }}{{ number_format($balance, 2) }} dh
                    </p>
                    <p class="text-xs text-gray-400">{{ $balance >= 0 ? 'à recevoir' : 'doit' }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Qui doit quoi à qui ?</h2>

        @if(empty($settlements))
            <div class="text-center py-8">
                <div class="text-4xl mb-3">✅</div>
                <p class="text-gray-500 text-sm">Tout est équilibré ! Aucun remboursement nécessaire.</p>
            </div>
        @else
            <div class="space-y-3 mb-6">
                @foreach($settlements as $s)
                <div class="flex items-center justify-between p-4 rounded-xl bg-amber-50 border border-amber-100">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xs font-bold">
                            {{ strtoupper(substr($s['from_name'], 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">
                                <span class="text-red-600">{{ $s['from_name'] }}</span>
                                doit payer à
                                <span class="text-green-600">{{ $s['to_name'] }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-base font-bold text-gray-800">{{ number_format($s['amount'], 2) }} dh</span>
                        
                        @if($user->id == $s['from'] || $user->id == $s['to'])
                        <form method="POST" action="{{ route('payments.store', $colocation->id) }}">
                            @csrf
                            <input type="hidden" name="payer_id" value="{{ $s['from'] }}" />
                            <input type="hidden" name="receiver_id" value="{{ $s['to'] }}" />
                            <input type="hidden" name="amount" value="{{ $s['amount'] }}" />
                            <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors"
                                    onclick="return confirm('Marquer ce paiement comme effectué ?')">
                                ✓ Marquer payé
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Enregistrer un paiement manuellement</h2>
        <form method="POST" action="{{ route('payments.store', $colocation->id) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            @csrf
            <select name="payer_id" required
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Payeur</option>
                @foreach($members as $m)
                    <option value="{{ $m->user_id }}">{{ $m->user->name }}</option>
                @endforeach
            </select>
            <select name="receiver_id" required
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
                <option value="">Bénéficiaire</option>
                @foreach($members as $m)
                    <option value="{{ $m->user_id }}">{{ $m->user->name }}</option>
                @endforeach
            </select>
            <input type="number" name="amount" step="0.01" min="0.01" placeholder="Montant (dh)" required
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg text-sm font-semibold transition-colors">
                Enregistrer
            </button>
        </form>
    </div>

</div>
@endsection