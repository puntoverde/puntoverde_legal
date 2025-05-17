<?php
namespace App\Entity;
use Illuminate\Database\Eloquent\Model;
use App\Entity\Accionista;
use App\Entity\Socio;

class Invitado extends Model 
{
    protected $table = 'historico_invitado_socio';
    protected $primaryKey = 'id';
    public $timestamps = false;

    // public function accionista()
    // {
    //     return $this->belongsTo(Accionista::class,'cve_dueno');
    // }

    //en si es el socio que es el invitado
    public function socio()
    {
        return $this->belongsTo(Socio::class,'cve_socio');
    }

     //es el socio que invita    
    public function socio_invita()
    {
        return $this->belongsTo(Socio::class,'cve_socio_invita');
    }

}