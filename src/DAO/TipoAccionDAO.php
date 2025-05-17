<?php
namespace App\DAO;
use App\Entity\TipoAccion;


class TipoAccionDAO {
    
    public static function getTipoAccion()
    {
        return TipoAccion::where('estatus',1)
        ->whereIn('cve_tipo_accion',[2,3])
        ->orderBy('nombre')
        ->select('cve_tipo_accion AS id','nombre')
        ->get();
    }
}
