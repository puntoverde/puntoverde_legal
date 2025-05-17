<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitudMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    protected $correo;
    protected $folio;
    protected $clave;
    protected $fecha;

    public function __construct($correo,$folio,$clave,$fecha)
    {
        //
       $this->correo=$correo;
       $this->folio=$folio;
       $this->clave=$clave;
       $this->fecha=$fecha;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('welcome')->with([
            "correo"=>$this->correo,
            "folio"=>$this->folio,
            "clave"=>$this->clave,
            "fecha"=>$this->fecha,
        ]);
    }
}
