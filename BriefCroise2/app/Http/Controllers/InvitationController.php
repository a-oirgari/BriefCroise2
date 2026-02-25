<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Colocation;
use App\Models\Invitation;
use App\Models\Membership;
use App\Http\Requests\StoreInvitationRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class InvitationController extends Controller
{
    public function store(StoreInvitationRequest $request, Colocation $colocation)
    {
        $user = User::find(session('user_id'));

        $membership = $colocation->activeMembers()->where('user_id', $user->id)->first();
        if (!$membership || $membership->role !== 'owner') {
            return back()->with('error', 'Seul l\'owner peut inviter des membres.');
        }

        $email = $request->email;

        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            $alreadyMember = $colocation->activeMembers()
                ->where('user_id', $existingUser->id)
                ->exists();
            if ($alreadyMember) {
                return back()->with('error', 'Cet utilisateur est déjà membre de la colocation.');
            }
        }

        $existingInvitation = $colocation->invitations()
            ->where('email', $email)
            ->where('status', 'pending')
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($existingInvitation) {
            return back()->with('error', 'Une invitation est déjà en attente pour cet email.');
        }

        $token = Str::random(64);

        Invitation::create([
            'colocation_id' => $colocation->id,
            'email'         => $email,
            'token'         => $token,
            'status'        => 'pending',
            'expires_at'    => Carbon::now()->addDays(7),
        ]);

        $inviteUrl = route('invitations.show', $token);
        try {
            Mail::raw(
                "Vous avez été invité à rejoindre la colocation \"{$colocation->name}\".\n\nCliquez ici pour accepter : {$inviteUrl}\n\nCe lien expire dans 7 jours.",
                function ($message) use ($email, $colocation) {
                    $message->to($email)
                            ->subject("Invitation – Colocation {$colocation->name}");
                }
            );
        } catch (\Exception $e) {
            
        }

        return back()->with('success', "Invitation envoyée à {$email}. Lien : {$inviteUrl}");
    }

    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)->with('colocation')->firstOrFail();

        if (!$invitation->isPending()) {
            return view('invitations.invalid', [
                'message' => 'Cette invitation est expirée ou déjà utilisée.',
            ]);
        }

        $user = User::find(session('user_id'));

        return view('invitations.show', compact('invitation', 'user'));
    }

    public function accept(string $token)
    {
        $invitation = Invitation::where('token', $token)->with('colocation')->firstOrFail();

        if (!$invitation->isPending()) {
            return redirect()->route('dashboard')->with('error', 'Invitation invalide ou expirée.');
        }

        $user = User::find(session('user_id'));

        if (!$user) {
            return redirect()->route('login')->with('error', 'Connectez-vous pour accepter l\'invitation.');
        }

        if ($user->email !== $invitation->email) {
            return back()->with('error', "Cette invitation est destinée à {$invitation->email}.");
        }

        if ($user->hasActiveColocation()) {
            return back()->with('error', 'Vous avez déjà une colocation active. Quittez-la d\'abord.');
        }

        Membership::create([
            'user_id'       => $user->id,
            'colocation_id' => $invitation->colocation_id,
            'role'          => 'member',
            'joined_at'     => Carbon::now(),
        ]);

        $invitation->update(['status' => 'accepted']);

        return redirect()->route('colocations.show', $invitation->colocation_id)
            ->with('success', 'Vous avez rejoint la colocation !');
    }

    public function refuse(string $token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if (!$invitation->isPending()) {
            return redirect()->route('dashboard')->with('error', 'Invitation invalide ou expirée.');
        }

        $invitation->update(['status' => 'refused']);

        return redirect()->route('dashboard')->with('success', 'Invitation refusée.');
    }
}