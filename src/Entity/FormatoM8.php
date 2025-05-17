<?php
namespace App\Entity;
use Illuminate\Database\Eloquent\Model;
use App\Entity\Accionista;

class FormatoM8 extends Model 
{
    protected $table = 'formato_m8';
    protected $primaryKey = 'id';
    public $timestamps = false;

    // public function accionista()
    // {
    //     return $this->belongsTo(Accionista::class,'cve_dueno');
    // }

}