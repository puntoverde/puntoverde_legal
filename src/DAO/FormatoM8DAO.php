<?php

namespace App\DAO;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FormatoM8DAO
{

   
   public function __construct()
   {
   }

   public static function allPersonas($nombre)
   {
     return DB::table("persona")
     ->join("socios","persona.cve_persona","socios.cve_persona")
     ->join("acciones","socios.cve_accion","acciones.cve_accion")
     ->where("socios.estatus",1)
     ->where("persona.estatus",1)
     ->whereRaw("CONCAT_WS(' ',nombre,apellido_paterno,apellido_materno) like ?",["%".$nombre."%"])
     ->select("socios.cve_socio","persona.cve_persona","persona.nombre","apellido_paterno","apellido_materno","socios.posicion")
     ->selectRaw("CONCAT(acciones.numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_socio")
     ->get();
   }

   public static function findPersonaById($id)
   {     
      return DB::table("persona")
      ->leftJoin("socios","persona.cve_persona","socios.cve_persona")
      ->leftJoin("acciones","socios.cve_accion","acciones.cve_accion")
      ->leftJoin("examen_medico","persona.cve_persona","examen_medico.id_persona")      
      ->select("socios.cve_socio","persona.cve_persona","persona.nombre","persona.apellido_paterno","persona.apellido_materno","examen_medico.id_examen_medico","acciones.cve_accion")
      ->selectRaw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' ELSE '' END) AS accion")
      ->where("socios.cve_socio",$id)
      ->first();


   }
   
   public static function registrarFormatoM8($p,$cve_colaborador,$filename)
   {
      
      // $folio=DB::table("formato_m8")->count();
      $cve_accion=DB::table("socios")->where("cve_socio",$p->cve_usuario)->value("cve_accion");

      $id_formato8= DB::table("formato_m8")->insertGetId([
         "cve_accion"=>$cve_accion,
         "cve_socio"=>$p->cve_usuario,
         "cve_colaborador"=>$cve_colaborador,
         // "folio"=>$folio+1,
         "folio"=>$p->folio??0,
         "observacion"=>$p->descripcion??'',
         "fecha_registro"=>$p->fecha_registro, 
         "id_tipo_formato_m8"=>$p->tipo_formato,
         "archivo"=>$filename??null,
         "estatus"=>1
      ]);
     return $id_formato8;
   }

   public static function getFormatoM8($id)
   {
       return DB::table('formato_m8')
       ->join("socios","formato_m8.cve_socio","socios.cve_socio")
       ->join("persona","socios.cve_persona","persona.cve_persona")
       ->join("colaborador","formato_m8.cve_colaborador","colaborador.id_colaborador")
       ->join("persona AS persona2","colaborador.cve_persona","persona2.cve_persona")
       ->leftJoin("pago","formato_m8.folio","pago.folio_m8")
         ->select(
            "formato_m8.id_formato_m8",
            "observacion",
            "fecha_registro",
            // "hora_inicio",
            // "hora_fin",
            "persona2.nombre",
            "persona2.apellido_paterno",
            "persona2.apellido_materno",
            "respuesta",
            "fecha_agrego",
            "archivo",
            "formato_m8.estatus",
            "pago.folio AS folio_pago_caja"
            )
         ->where('formato_m8.cve_socio', $id)
         ->orderBy("fecha_registro","desc")        
         ->get();    
   }


   public static function registrarRespuesta($id,$p)
   {

      $rowAffect= DB::table("formato_m8")->where("id_formato_m8",$id)->update([
         "respuesta"=>$p->respuesta??'',
         "fecha_agrego"=>$p->fecha_agrego,
         "estatus"=>$p->estatus
      ]);
     return $rowAffect;
   }

   public static function saveFileName($id,$p)
   {
      
      $rowAffect= DB::table("formato_m8")->where("id_formato_m8",$id)->update(["archivo"=>$p]);    
     return $rowAffect;
   }


   public static function getTiposFormtoM8()
   {
       return DB::table('tipo_formato_m8')       
         ->select("id_tipo_formato_m8","tipo")
         ->get();    
   }

}


