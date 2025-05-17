<?php

namespace App\DAO;

use App\Entity\Accion;
use Illuminate\Support\Facades\DB;


class FotosAccesoSociosDAO
{

   public function __construct()
   {
   }

   public static function crearFotoAcceso($cve_socio,$foto)
   {
      return DB::table("socios_foto_acceso")->insertGetId([
         "cve_socio"=>$cve_socio,
         "foto_completa"=>$foto,         
         "foto_completa_auto"=>"",
         "estatus"=>1
      ]);
   }


   public static function actualizarFotoAcceso($id,$p)
   {

      $accion = Accion::find($id);
      if ($p->estatus ?? 0) $accion->estatus = $p->estatus;
      if ($p->cve_tipo_accion ?? false) $accion->cve_tipo_accion = $p->cve_tipo_accion;
      if ($p->fecha_adquisicion ?? false) $accion->fecha_adquisicion = $p->fecha_adquisicion;
      if ($p->motivo ?? false) $accion->motivo = $p->motivo;
      if ($p->persona_motivo ?? false) $accion->persona_motivo = $p->persona_motivo;
      $ok = $accion->save();

      return $ok;
   }

   public static function getFotoAcceso($id)
   {
      return DB::table("socios_foto_acceso")->where("cve_socio",$id)->value("foto_completa");
   }
}
