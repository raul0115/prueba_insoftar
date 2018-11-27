<?php

namespace App\Mail;

use App\Mail\UserCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $name;
    private $email;
    private $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $email,$password)
    {
        
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
                ->subject("Welcome to Insoftar")
                ->markdown('emails.user_created')
                ->with('name',$this->name)
                ->with('email',$this->email)
                ->with('password',$this->password);
    }
}
