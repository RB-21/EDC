<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UpdatePasswordMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $nama;
    protected $tanggal;
    protected $ipaddress;
    protected $device;
    protected $os;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($nama, $ipaddress, $tanggal,$device, $os)
    {
        $this->nama = $nama;
        $this->tanggal = $tanggal;
        $this->ipaddress = $ipaddress;
        $this->device = $device;
        $this->os = $os;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Notifikasi Keamanan - Update Password Berhasil',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'email_update_password_template',
            with: [
                'nama' => $this->nama,
                'tanggal' => $this->tanggal,
                'ipaddress' => $this->ipaddress,
                'device' => $this->device,
                'os' => $this->os,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
