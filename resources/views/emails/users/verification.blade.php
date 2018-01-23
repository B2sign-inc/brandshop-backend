@component('mail::message')
Hi, {{ $user->name }},

Thank you for creating an account with us. Don't forget to complete your registration!

Please click on the link below or copy it into the address bar of your browser to confirm your email address:

@component('mail::button', ['url' => $verifyUrl, 'color' => 'blue'])
    Verify
@endcomponent
@endcomponent