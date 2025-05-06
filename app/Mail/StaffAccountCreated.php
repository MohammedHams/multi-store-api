<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StaffAccountCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;
    public $store;
    public $verificationUrl;

    public function __construct(User $user, string $password, Store $store, string $verificationUrl)
    {
        $this->user = $user;
        $this->password = $password;
        $this->store = $store;
        $this->verificationUrl = $verificationUrl;
    }

    public function build()
    {
        return $this->subject('تفعيل حساب الموظف - ' . $this->store->name)
            ->markdown('emails.staff-account-created');
    }
}
