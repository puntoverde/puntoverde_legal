<?php

namespace App\DAO;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HistorialMedicoDAO
{

   
   public function __construct()
   {
   }

   public static function allPersonas($nombre)
   {
     return DB::table("persona")
     ->whereRaw("CONCAT_WS(' ',nombre,apellido_paterno,apellido_materno) like ?",["%".$nombre."%"])
     ->select("cve_persona","nombre","apellido_paterno","apellido_materno")
     ->get();
   }

   public static function findPersonaById($id)
   {     
      return DB::table("persona")
      ->leftJoin("socios","persona.cve_persona","socios.cve_persona")
      ->leftJoin("acciones","socios.cve_accion","acciones.cve_accion")
      ->leftJoin("examen_medico","persona.cve_persona","examen_medico.id_persona")      
      ->select("persona.cve_persona","persona.nombre","persona.apellido_paterno","persona.apellido_materno","examen_medico.id_examen_medico")
      ->selectRaw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' ELSE '' END) AS accion")
      ->where("persona.cve_persona",$id)
      ->first();


   }
   
   public static function registrarPercance($p)
   {
      $id_antecedentes= DB::table("historial_medico")->insertGetId([
         "id_persona"=>$p->id_persona,
         "id_persona_atiende"=>$p->encargado,
         "descripcion"=>$p->descripcion,
         "fecha_registro"=>$p->fecha_registro, 
         "hora_inicio"=>$p->hora_inicio, 
         "hora_fin"=>$p->hora_fin, 
         "fecha_agrego"=>Carbon::now(), 
      ]);
     return $id_antecedentes;
   }
   
   public static function actualizarPercance($id,$p)
   {
      $id_antecedentes= DB::table("examen_medico")
      ->where("id_examen_medico",$id)
      ->update([
         "observacion"=>$p->observacion,
         "factores_riesgo"=>$p->factores_riesgo,
         "resultado"=>$p->resultado,
         "motivo"=>$p->motivo,
         "estatus"=>5,
      ]);
     return $id_antecedentes;
   }

   public static function getPercances($id)
   {
       return DB::table('historial_medico')
       ->join("persona","historial_medico.id_persona","persona.cve_persona")
       ->join("persona AS persona2","historial_medico.id_persona_atiende","persona2.cve_persona")
         ->select("descripcion","fecha_registro","hora_inicio","hora_fin","persona2.nombre","persona2.apellido_paterno","persona2.apellido_materno")
         ->where('historial_medico.id_persona', $id)
         ->get();    
   }
  
  
   public static function getReporteHistorialMedico($id)
   {
      $examen_medico=DB::table('examen_medico')
      ->join("examen_medico_antecedentes" , "examen_medico.id_examen_medico","examen_medico_antecedentes.id_examen_medico")
      ->join("examen_medico_historia_medica" , "examen_medico.id_examen_medico","examen_medico_historia_medica.id_examen_medico")
      ->join("examen_medico_alimentacion" , "examen_medico.id_examen_medico","examen_medico_alimentacion.id_examen_medico")
      ->join("examen_medico_exploracion" , "examen_medico.id_examen_medico","examen_medico_exploracion.id_examen_medico")         
      ->where('examen_medico.id_persona', $id)
      ->first(); 
         
         
      $historial_medico=DB::table('historial_medico')
      ->select("descripcion","fecha_registro","hora_inicio","hora_fin")
      ->where('historial_medico.id_persona', $id)
      ->orderBy("fecha_registro","desc")
      ->get(); 

      return ["examen"=>$examen_medico,"historial"=>$historial_medico];
   }

}


