<?php
namespace App\Entity;
use Illuminate\Database\Eloquent\Model;
use App\Entity\Accionista;

class UsuarioObservacion extends Model 
{
    protected $table = 'usuario_observacion';
    protected $primaryKey = 'id';
    public $timestamps = false;

    // public function accionista()
    // {
    //     return $this->belongsTo(Accionista::class,'cve_dueno');
    // }

}