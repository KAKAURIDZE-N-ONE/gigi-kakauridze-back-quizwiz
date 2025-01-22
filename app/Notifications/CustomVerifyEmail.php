<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    protected $verificationUrl;

    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(120),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $frontendUrl = config('app.frontend_url');
        $appUrl = config('app.url');
        $customVerificationUrl = str_replace(
            $appUrl,
            $frontendUrl,
            $verificationUrl
        );
        $this->verificationUrl = $customVerificationUrl;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
                    ->subject('Email Verification')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Thank you for registering with us. Please click the button below to verify your email address.')
                    ->action('Verify Email', $this->verificationUrl)
                    ->line('If you did not create an account, no further action is required.')
                    ->line('Thank you for using our application!')
                    ->markdown('emails.verify', [
                        'name' => $notifiable->name,
                        'verificationUrl' => $this->verificationUrl,
                    ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            // Any other data you'd like to include
        ];
    }
}
