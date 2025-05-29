<?php

namespace MichaelDzjap\TwoFactorAuth\Providers;

use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
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
        $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->setTwoFactorAuthId($token);
        $data = [
            'user' => $user,
            'token' => $token,
            'expires_in' => config('twofactor-auth.token_lifetime', 10), // default 10 mins
            'loginUrl' => url(config('twofactor-auth.routes.get.url')),
        ];
        // Technically, this means Simple Mail Service for this
        $markdown = app(Markdown::class);
        $html = $markdown->render(config('twofactor-auth.providers.mail.template'), $data);
        Mail::send([], [], function ($message) use ($html, $user) {
            $message->to($user->email)
                    ->subject('Your ' . config('app.name') . ' Login Code')
                    ->html((string) $html);
        });
    }
}
