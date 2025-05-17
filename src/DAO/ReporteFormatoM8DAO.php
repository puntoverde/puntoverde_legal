<?php

namespace App\DAO;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use stdClass;

class ReporteFormatoM8DAO
{

   
   public function __construct()
   {
   }

   public static function getFormatoM8($filter)
   {


   $query=DB::table("formato_m8")
     ->join("tipo_formato_m8","tipo_formato_m8.id_tipo_formato_m8","formato_m8.id_tipo_formato_m8")
     ->join("socios","formato_m8.cve_socio","socios.cve_socio")
     ->join("persona","persona.cve_persona","socios.cve_persona")     
     ->join("acciones","acciones.cve_accion","formato_m8.cve_accion")
     ->leftJoin("pago","formato_m8.folio","pago.folio_m8")
     ->select(
     "socios.posicion",
     "persona.nombre",
     "persona.apellido_paterno",
     "persona.apellido_materno",
     "tipo_formato_m8.tipo",
     "tipo_formato_m8.id_tipo_formato_m8",
     "formato_m8.observacion",
     "formato_m8.archivo",
     "formato_m8.fecha_registro",
     "formato_m8.respuesta",
     "formato_m8.fecha_agrego",
     "formato_m8.estatus",
     "pago.folio AS folio_pago_caja"
     )
     ->selectRaw("CONCAT(acciones.numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion");

    //  if($filter->nombre??false){
    //      $query->whereRaw( "CONCAT_WS(' ',nombre,apellido_paterno,apellido_materno) like ?",["%".$filter->nombre."%"]);
    //  }

    //  if(($filter->fecha_inicio??false) && ($filter->fecha_fin??false))
    //  {
    //   $query->whereBetween("fecha_registro",[$filter->fecha_inicio,$filter->fecha_fin]);
    //  }

    //  else if($filter->fecha_inicio??false)
    //  {
    //   $query->where("fecha_registro",$filter->fecha_inicio);
    //  }

    //  else if($filter->fecha_fin??false)
    //  {
    //   $query->where("fecha_registro",'<=',$filter->fecha_fin);
    //  }

    //  if($filter !=new stdClass())
    //  return $query->get();
    //  else return [];
     return $query->get();


   }  
   
   
   public static function getFormatoM8ByAccion()
   {


     return DB::table("formato_m8")
     ->join("acciones" , "formato_m8.cve_accion","acciones.cve_accion")
     ->leftJoin("pago","formato_m8.folio","pago.folio_m8")
     ->groupBy("formato_m8.cve_accion")
     ->select("formato_m8.cve_accion","pago.folio AS folio_pago_caja")
     ->selectRaw("count(formato_m8.cve_accion) as full_m8")
     ->selectRaw("concat(acciones.numero_accion,case acciones.clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' else '' end ) as numero_accion")
     ->get();

   }

   public static function getFormatosM8ByAccionDetalle($cve_accion)
   {
     return DB::table("formato_m8")
     ->where("cve_accion",$cve_accion)
     ->select("observacion","fecha_registro","archivo")
     ->get();
   }

}


