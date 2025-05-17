<?php
namespace App\DAO;

use Illuminate\Support\Facades\DB;

class ApartadoEstatusDAO
{

    public function __construct(){
    }

    public static function getApartadosTenis($p){
        
        $query = DB::table("apartados")
        ->join("validacion_apartado", "apartados.cve_apartado", "validacion_apartado.cve_apartado")
        ->join("equipo", "apartados.cve_equipo", "equipo.cve_equipo")
        ->join("socios", "validacion_apartado.cve_socio", "socios.cve_socio")
        ->join("persona", "socios.cve_persona", "persona.cve_persona")
        ->join("acciones", "socios.cve_accion", "acciones.cve_accion")
        ->leftjoin("acceso_socio", "socios.cve_socio", "acceso_socio.cve_socio")
        ->where ("apartados.fecha_registro",'>=', $p  )
        ->whereIn ("apartados.cve_equipo",[47,48] )
        ->where("acceso_socio.fecha",'>=', $p);
       
        $query->select("equipo.descripcion", "apartados.cve_apartado",  "apartados.fecha_registro", "acciones.numero_accion", "socios.posicion")
        ->addSelect("persona.nombre", "apartados.fecha_inicio","apartados.fecha_fin","apartados.estatus","acceso_socio.entrada","acceso_socio.salida","validacion_apartado.cve_apartado");

        // dd($query->toSql());

        $apartados = $query->get();

        

        return $apartados;
    }
}