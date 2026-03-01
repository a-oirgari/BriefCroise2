@component('mail::message')
# Vous avez été invité !

Bonjour,

Vous avez été invité à rejoindre la colocation **{{ $colocationName }}** sur EasyColoc.

@component('mail::button', ['url' => $inviteUrl, 'color' => 'primary'])
Voir l'invitation
@endcomponent

Ce lien est valable **7 jours**.

Si vous n'êtes pas concerné par cette invitation, ignorez cet email.

Cordialement,
**L'équipe EasyColoc**
@endcomponent