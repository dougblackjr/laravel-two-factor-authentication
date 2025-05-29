<?php

namespace MichaelDzjap\TwoFactorAuth\Providers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use MichaelDzjap\TwoFactorAuth\Contracts\TwoFactorProvider;
use MichaelDzjap\TwoFactorAuth\Models\TwoFactorAuth;

class MailProvider extends BaseProvider implements TwoFactorProvider
{
    /**
     * {@inheritdoc}
     */
    public function register($user): void
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function unregister($user)
    {
        //
    }

    /**
     * {@inheritdoc}
     */
    public function verify($user, string $token)
    {
        $token = $user->twoFactorAuth()->first();
        return $token && Str::lower($token->id) === Str::lower($token);
    }

    /**
     * {@inheritdoc}
     */
    public function sendSMSToken($user): void
    {
        $token = Str::random(6);
        $user->setTwoFactorAuthId($token);
        $data = [
            'user' => $user,
            'token' => $token,
            'expires_in' => config('twofactor-auth.token_lifetime', 10), // default 10 mins
            'loginUrl' => url('/login')
        ];
        // Technically, this means Simple Mail Service for this
        Mail::send(config('twofactor-auth.providers.mail.template'), $data, function ($message) {
            $message->to($user->email)
                    ->subject('Your ' . config('app.name') . ' Login Code');
        });
    }
}
