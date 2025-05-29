@component('mail::message')
# Your {{ config('app.name') }} Login Token

Hello {{ $user->name }},

Here is your **two-factor authentication token**:

@component('mail::panel')
{{ $token }}
@endcomponent

This token will expire in {{ $expires_in }} minutes.

If you did not request this, please secure your account immediately.

@component('mail::button', ['url' => $loginUrl])
Login Now
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
