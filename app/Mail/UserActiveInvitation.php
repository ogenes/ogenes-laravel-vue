<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserActiveInvitation extends Mailable
{
    use Queueable, SerializesModels;

    protected string $email;
    protected string $url;

    /**
     * Create a new message instance.
     *
     * @param string $email
     * @param string $url
     */
    public function __construct(string $email, string $url)
    {
        $this->url = $url;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->view('emails.user_active_invitation')
            ->subject('请激活您的账户！')
            ->with([
                'email' => $this->email,
                'url' => $this->url,
            ]);
    }
}
