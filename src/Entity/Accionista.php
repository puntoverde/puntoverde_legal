<?php
namespace App\Entity;
use Illuminate\Database\Eloquent\Model;
use App\Entity\Persona;
use App\Entity\Direccion;

class Accionista extends Model 
{
    protected $table = 'dueno';
    protected $primaryKey = 'cve_dueno';
    public $timestamps = false;
    

    public function persona()
    {
        return $this->belongsTo(Persona::class,'cve_persona');
    }

    public function direccion()
    {
        return $this->belongsTo(Direccion::class,'cve_direccion');
    }

    public function documentos()
    {
        return $this->belongsToMany(Documento::class,'documento_dueno','cve_dueno','cve_documento')->withPivot('cve_documento_dueno','ruta', 'estatusDocumento','nombre');
    }
}