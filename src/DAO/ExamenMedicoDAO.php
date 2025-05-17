<?php

namespace App\DAO;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamenMedicoDAO
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

      /*SELECT 
persona.cve_persona,
persona.nombre,
persona.apellido_paterno,
persona.apellido_materno,
CONCAT(acciones.numero_accion,CASE acciones.clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' ELSE '' END) AS accion,
examen_medico.id_examen_medico
FROM persona 
left JOIN socios ON persona.cve_persona=socios.cve_persona
LEFT JOIN acciones ON socios.cve_accion=acciones.cve_accion
LEFT JOIN examen_medico ON persona.cve_persona=examen_medico.id_persona
WHERE persona.cve_persona=100*/
      return DB::table("persona")
      ->leftJoin("socios","persona.cve_persona","socios.cve_persona")
      ->leftJoin("acciones","socios.cve_accion","acciones.cve_accion")
      ->leftJoin("examen_medico","persona.cve_persona","examen_medico.id_persona")      
      ->select("persona.cve_persona","persona.nombre","persona.apellido_paterno","persona.apellido_materno","examen_medico.id_examen_medico")
      ->selectRaw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' ELSE '' END) AS accion")
      ->where("persona.cve_persona",$id)
      ->first();


   }
   
   public static function crearExamenMedico($p)
   {
      $id_antecedentes= DB::table("examen_medico")->insertGetId([
         "id_persona"=>$p->id_persona,
         "encargado"=>$p->encargado,
         "estatus"=>1, 
      ]);
     return $id_antecedentes;
   }
   
   public static function finalizarExamen($id,$p)
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

   public static function getExamenMedico($id)
   {

      // SELECT id_examen_medico_antecedentes,cardio_vascular,presion,diabetes,cancer,tuberculosis,neurologica,asma,reumatica 
      // FROM examen_medico_antecedentes WHERE id_examen_medico IS NULL

       return DB::table('examen_medico')
         ->select("observacion","factores_riesgo","resultado","motivo","estatus")
         ->where('id_examen_medico', $id)
         ->first();    
   }


   //antecedentes
   public static function getAntecedentesFamiliares($id)
   {

      // SELECT id_examen_medico_antecedentes,cardio_vascular,presion,diabetes,cancer,tuberculosis,neurologica,asma,reumatica 
      // FROM examen_medico_antecedentes WHERE id_examen_medico IS NULL

       return DB::table('examen_medico_antecedentes')
         ->select("id_examen_medico_antecedentes","cardio_vascular","presion","diabetes","cancer","tuberculosis","neurologica","asma","reumatica")
         ->where('id_examen_medico', $id)
         ->first();    
   }

   public static function saveAntecedentesFamiliares($p)
   {
      $id_antecedentes= DB::table("examen_medico_antecedentes")->insertGetId([
         "id_examen_medico"=>$p->id_examen_medico,
         "cardio_vascular"=>$p->cardiovascular,
         "presion"=>$p->presion,
         "diabetes"=>$p->diabetes,
         "cancer"=>$p->cancer,
         "tuberculosis"=>$p->tuberculosis,
         "neurologica"=>$p->neurologica,
         "asma"=>$p->asma,
         "reumatica"=>$p->reumatica    
      ]);
     return $id_antecedentes;
   }


   public static function updateAntecedentesFamiliares($p,$id)
   {
      $rowAffect= DB::table("examen_medico_antecedentes")
      ->where("id_examen_medico_antecedentes",$id)
      ->update([
         "cardio_vascular"=>$p->cardiovascular,
         "presion"=>$p->presion,
         "diabetes"=>$p->diabetes,
         "cancer"=>$p->cancer,
         "tuberculosis"=>$p->tuberculosis,
         "neurologica"=>$p->neurologica,
         "asma"=>$p->asma,
         "reumatica"=>$p->reumatica    
      ]);
     return $rowAffect;
   }

    //historia medica
    public static function getHistoriaMedica($id)
    {
 
       // SELECT id_examen_medico_antecedentes,cardio_vascular,presion,diabetes,cancer,tuberculosis,neurologica,asma,reumatica 
       // FROM examen_medico_antecedentes WHERE id_examen_medico IS NULL
 
        return DB::table('examen_medico_historia_medica')
          ->select("*")
          ->where('id_examen_medico', $id)
          ->first();    
    }
 
    public static function savetHistoriaMedica($p)
    {
       $id_historico= DB::table("examen_medico_historia_medica")->insertGetId([
         "id_examen_medico"=>$p->id_examen_medico,
          "alergias"=>$p->alergias,
          "asma"=>$p->asma,
          "cardiaca"=>$p->cardiaca,
          "torax"=>$p->torax,
          "epilepsia"=>$p->epilepsia,
          "diabetes"=>$p->diabetes,
          "cancer"=>$p->cancer,
          "ulcera"=>$p->ulcera,    
          "rinon"=>$p->riñon,
          "perdida_peso"=>$p->perdida_peso,
          "visual"=>$p->visuales,
          "audicion"=>$p->audicion,
          "reumaticos"=>$p->reumaticos,
          "cirugias"=>$p->cirugias,
          "menstruales"=>$p->menstruales,
          "esguince"=>$p->esguinses,
          "fuma"=>$p->fuma,
          "ingestion_alcohol"=>$p->alcohol,
          "primera_mestruacion"=>$p->edad_mestruacion,
          "n_embarazos"=>$p->numero_embarazo,
          "n_partos"=>$p->numero_partos,
          "n_cesareas"=>$p->cesareas,
          "n_abortos"=>$p->numero_abortos,
       ]);
      return $id_historico;
    }
   
    public static function updatetHistoriaMedica($p,$id)
    {
       $rowAffect= DB::table("examen_medico_historia_medica")
       ->where("id_examen_medico_historia_medica",$id)
       ->update([
         "alergias"=>$p->alergias,
         "asma"=>$p->asma,
         "cardiaca"=>$p->cardiaca,
         "torax"=>$p->torax,
         "epilepsia"=>$p->epilepsia,
         "diabetes"=>$p->diabetes,
         "cancer"=>$p->cancer,
         "ulcera"=>$p->ulcera,    
         "rinon"=>$p->riñon,
         "perdida_peso"=>$p->perdida_peso,
         "visual"=>$p->visuales,
         "audicion"=>$p->audicion,
         "reumaticos"=>$p->reumaticos,
         "cirugias"=>$p->cirugias,
         "menstruales"=>$p->menstruales,
         "esguince"=>$p->esguinses,
         "fuma"=>$p->fuma,
         "ingestion_alcohol"=>$p->alcohol,
         "primera_mestruacion"=>$p->edad_mestruacion,
         "n_embarazos"=>$p->numero_embarazo,
         "n_partos"=>$p->numero_partos,
         "n_cesareas"=>$p->cesareas,
         "n_abortos"=>$p->numero_abortos,   
       ]);
      return $rowAffect;
    }


    //alimetacion habitual
    public static function getAlimentacionHabitual($id)
    {
 
       // SELECT id_examen_medico_antecedentes,cardio_vascular,presion,diabetes,cancer,tuberculosis,neurologica,asma,reumatica 
       // FROM examen_medico_antecedentes WHERE id_examen_medico IS NULL
 
        return DB::table('examen_medico_alimentacion')
          ->select("*")
          ->where('id_examen_medico', $id)
          ->first();    
    }
 
    public static function saveAlimentacionHabitual($p)
    {
       $id_historico= DB::table("examen_medico_alimentacion")->insertGetId([
          "id_examen_medico"=>$p->id_examen_medico,
          "desayuno"=>$p->desayuno,
          "comida"=>$p->comida,
          "cena"=>$p->cena,
          "peso"=>$p->peso,
          "estatura"=>$p->estatura,
          "fc"=>$p->fc,
          "pa"=>$p->pa,
          "edad_inicio"=>$p->edad_ejercicio,    
          "deporte_inicia"=>$p->deporte_inicia,
          "deporte_actual"=>$p->deporte_actual,
          "frecuencia"=>$p->frecuencia,
          "instensidad"=>$p->intesidad,
       ]);
      return $id_historico;
    }
 
    public static function updateAlimentacionHabitual($p,$id)
    {
       $rowAffect= DB::table("examen_medico_alimentacion")
       ->where("id_examen_medico_alimentacion",$id)
       ->update([
         "desayuno"=>$p->desayuno,
         "comida"=>$p->comida,
         "cena"=>$p->cena,
         "peso"=>$p->peso,
         "estatura"=>$p->estatura,
         "fc"=>$p->fc,
         "pa"=>$p->pa,
         "edad_inicio"=>$p->edad_ejercicio,    
         "deporte_inicia"=>$p->deporte_inicia,
         "deporte_actual"=>$p->deporte_actual,
         "frecuencia"=>$p->frecuencia,
         "instensidad"=>$p->intesidad,  
       ]);
      return $rowAffect;
    }



     //exploracion fisica
     public static function getExploracionFisica($id)
     {
  
        // SELECT id_examen_medico_antecedentes,cardio_vascular,presion,diabetes,cancer,tuberculosis,neurologica,asma,reumatica 
        // FROM examen_medico_antecedentes WHERE id_examen_medico IS NULL
  
         return DB::table('examen_medico_exploracion')
           ->select("*")
           ->where('id_examen_medico', $id)
           ->first();    
     }
  
     public static function saveExploracionFisica($p)
     {
        $id_historico= DB::table("examen_medico_exploracion")->insertGetId([
           "id_examen_medico"=>$p->id_examen_medico,
           "cabeza_ojos"=>$p->cabeza_ojos,
           "cabeza_ojos_txt"=>$p->cabeza_ojos_txt??'',
           "cabeza_nariz"=>$p->cabeza_nariz,
           "cabeza_nariz_txt"=>$p->cabeza_nariz_txt??'',
           "cabeza_faringe"=>$p->cabeza_faringe,
           "cabeza_faringe_txt"=>$p->cabeza_faringe_txt??'',
           "cabeza_boca"=>$p->cabeza_boca,
           "cabeza_boca_txt"=>$p->cabeza_boca_txt??'',    
           "cabeza_dentadura"=>$p->cabeza_dentadura,
           "cabeza_dentadura_txt"=>$p->cabeza_dentadura_txt??'',
           "cabeza_audicion"=>$p->cabeza_audicion,
           "cabeza_audicion_txt"=>$p->cabeza_audicion_txt??'',
           "cabeza_vision"=>$p->cabeza_vision,
           "cabeza_vision_txt"=>$p->cabeza_vision_txt??'',
           
           "cuello_forma"=>$p->cuello_forma,
           "cuello_forma_txt"=>$p->cuello_forma_txt??'',
           "cuello_volumen"=>$p->cuello_volumen,
           "cuello_volumen_txt"=>$p->cuello_volumen_txt??'',
           "cuello_adenome"=>$p->cuello_adenomegalia,
           "cuello_adenome_txt"=>$p->cuello_adenomegalia_txt??'',
           "cuello_tiroides"=>$p->cuello_tiroides,
           "cuello_tiroides_txt"=>$p->cuello_tiroides_txt??'',
           "cuello_tumuraciones"=>$p->cuello_tumuraciones,
           "cuello_tumuraciones_txt"=>$p->cuello_tumuraciones_txt??'',
           "cuello_pulsos"=>$p->cuello_pulsos,
           "cuello_pulsos_txt"=>$p->cuello_pulsos_txt??'',
           
           "torax_forma"=>$p->torax_forma,
           "torax_forma_txt"=>$p->torax_forma_txt??'',
           "torax_volumen"=>$p->torax_volumen,
           "torax_volumen_txt"=>$p->torax_volumen_txt??'',
           "torax_ampliacion"=>$p->torax_ampliacion,
           "torax_ampliacion_txt"=>$p->torax_ampliacion_txt??'',
           "torax_amplexa"=>$p->torax_amplexa,
           "torax_amplexa_txt"=>$p->torax_amplexa_txt??'',
           "torax_precusion"=>$p->torax_precusion,
           "torax_precusion_txt"=>$p->torax_precusion_txt??'',
           "torax_cardiaco"=>$p->torax_cardiacos,
           "torax_cardiaco_txt"=>$p->torax_cardiacos_txt??'',
           "torax_pulmonares"=>$p->torax_pulmunares,
           "torax_pulmonares_txt"=>$p->torax_pulmunares_txt??'',
           
           "abdomen_forma"=>$p->abdomen_forma,
           "abdomen_forma_txt"=>$p->abdomen_forma_txt??'',
           "abdomen_volumen"=>$p->abdomen_volumen,
           "abdomen_volumen_txt"=>$p->abdomen_volumen_txt??'',
           "abdomen_palpitacion"=>$p->abdomen_palpitacion,
           "abdomen_palpitacion_txt"=>$p->abdomen_palpitacion_txt??'',           
           "abdomen_percusion"=>$p->abdomen_percusion,
           "abdomen_percusion_txt"=>$p->abdomen_percusion_txt??'',
           "abdomen_peristalsis"=>$p->abdomen_peristalsis,
           "abdomen_peristalsis_txt"=>$p->abdomen_peristalsis_txt??'',

           "toracicas_forma"=>$p->toracicas_forma,
           "toracicas_forma_txt"=>$p->toracicas_forma_txt??'',
           "toracicas_volumen"=>$p->toracicas_volumen,
           "toracicas_volumen_txt"=>$p->toracicas_volumen_txt??'',
           "toracicas_movilidad"=>$p->toracicas_articular,
           "toracicas_movilidad_txt"=>$p->toracicas_articular_txt??'',
           "toracicas_pulso"=>$p->toracicas_pulsos,
           "toracicas_pulso_txt"=>$p->toracicas_pulsos_txt??'',
           "toracicas_sensibilidad"=>$p->toracicas_sensibilidad,
           "toracicas_sensibilidad_txt"=>$p->toracicas_sensibilidad_txt??'',
           "toracicas_osteo"=>$p->toracicas_osteotendino,
           "toracicas_osteo_txt"=>$p->toracicas_osteotendino_txt??'',
           
           "pelvicas_forma"=>$p->pelvis_forma,
           "pelvicas_forma_txt"=>$p->pelvis_forma_txt??'',
           "pelvicas_volumen"=>$p->pelvis_volumen,
           "pelvicas_volumen_txt"=>$p->pelvis_volumen_txt??'',
           "pelvicas_articular"=>$p->pelvis_articular,
           "pelvicas_articular_txt"=>$p->pelvis_articular_txt??'',
           "pelvicas_sensibilidad"=>$p->pelvis_sensibilidad,
           "pelvicas_sensibilidad_txt"=>$p->pelvis_sensibilidad_txt??'',
           "pelvicas_osteo"=>$p->pelvis_osteotendino,
           "pelvicas_osteo_txt"=>$p->pelvis_osteotendino_txt??'',
        ]);
       return $id_historico;
     }
  
     public static function updateExploracionFisica($p,$id)
     {
        $rowAffect= DB::table("examen_medico_exploracion")
        ->where("id_examen_medico_exploracion",$id)
        ->update([
         "cabeza_ojos"=>$p->cabeza_ojos,
           "cabeza_ojos_txt"=>$p->cabeza_ojos_txt??'',
           "cabeza_nariz"=>$p->cabeza_nariz,
           "cabeza_nariz_txt"=>$p->cabeza_nariz_txt??'',
           "cabeza_faringe"=>$p->cabeza_faringe,
           "cabeza_faringe_txt"=>$p->cabeza_faringe_txt??'',
           "cabeza_boca"=>$p->cabeza_boca,
           "cabeza_boca_txt"=>$p->cabeza_boca_txt??'',    
           "cabeza_dentadura"=>$p->cabeza_dentadura,
           "cabeza_dentadura_txt"=>$p->cabeza_dentadura_txt??'',
           "cabeza_audicion"=>$p->cabeza_audicion,
           "cabeza_audicion_txt"=>$p->cabeza_audicion_txt??'',
           "cabeza_vision"=>$p->cabeza_vision,
           "cabeza_vision_txt"=>$p->cabeza_vision_txt??'',
           
           "cuello_forma"=>$p->cuello_forma,
           "cuello_forma_txt"=>$p->cuello_forma_txt??'',
           "cuello_volumen"=>$p->cuello_volumen,
           "cuello_volumen_txt"=>$p->cuello_volumen_txt??'',
           "cuello_adenome"=>$p->cuello_adenomegalia,
           "cuello_adenome_txt"=>$p->cuello_adenomegalia_txt??'',
           "cuello_tiroides"=>$p->cuello_tiroides,
           "cuello_tiroides_txt"=>$p->cuello_tiroides_txt??'',
           "cuello_tumuraciones"=>$p->cuello_tumuraciones,
           "cuello_tumuraciones_txt"=>$p->cuello_tumuraciones_txt??'',
           "cuello_pulsos"=>$p->cuello_pulsos,
           "cuello_pulsos_txt"=>$p->cuello_pulsos_txt??'',
           
           "torax_forma"=>$p->torax_forma,
           "torax_forma_txt"=>$p->torax_forma_txt??'',
           "torax_volumen"=>$p->torax_volumen,
           "torax_volumen_txt"=>$p->torax_volumen_txt??'',
           "torax_ampliacion"=>$p->torax_ampliacion,
           "torax_ampliacion_txt"=>$p->torax_ampliacion_txt??'',
           "torax_amplexa"=>$p->torax_amplexa,
           "torax_amplexa_txt"=>$p->torax_amplexa_txt??'',
           "torax_precusion"=>$p->torax_precusion,
           "torax_precusion_txt"=>$p->torax_precusion_txt??'',
           "torax_cardiaco"=>$p->torax_cardiacos,
           "torax_cardiaco_txt"=>$p->torax_cardiacos_txt??'',
           "torax_pulmonares"=>$p->torax_pulmunares,
           "torax_pulmonares_txt"=>$p->torax_pulmunares_txt??'',
           
           "abdomen_forma"=>$p->abdomen_forma,
           "abdomen_forma_txt"=>$p->abdomen_forma_txt??'',
           "abdomen_volumen"=>$p->abdomen_volumen,
           "abdomen_volumen_txt"=>$p->abdomen_volumen_txt??'',
           "abdomen_palpitacion"=>$p->abdomen_palpitacion,
           "abdomen_palpitacion_txt"=>$p->abdomen_palpitacion_txt??'',           
           "abdomen_percusion"=>$p->abdomen_percusion,
           "abdomen_percusion_txt"=>$p->abdomen_percusion_txt??'',
           "abdomen_peristalsis"=>$p->abdomen_peristalsis,
           "abdomen_peristalsis_txt"=>$p->abdomen_peristalsis_txt??'',

           "toracicas_forma"=>$p->toracicas_forma,
           "toracicas_forma_txt"=>$p->toracicas_forma_txt??'',
           "toracicas_volumen"=>$p->toracicas_volumen,
           "toracicas_volumen_txt"=>$p->toracicas_volumen_txt??'',
           "toracicas_movilidad"=>$p->toracicas_articular,
           "toracicas_movilidad_txt"=>$p->toracicas_articular_txt??'',
           "toracicas_pulso"=>$p->toracicas_pulsos,
           "toracicas_pulso_txt"=>$p->toracicas_pulsos_txt??'',
           "toracicas_sensibilidad"=>$p->toracicas_sensibilidad,
           "toracicas_sensibilidad_txt"=>$p->toracicas_sensibilidad_txt??'',
           "toracicas_osteo"=>$p->toracicas_osteotendino,
           "toracicas_osteo_txt"=>$p->toracicas_osteotendino_txt??'',
           
           "pelvicas_forma"=>$p->pelvis_forma,
           "pelvicas_forma_txt"=>$p->pelvis_forma_txt??'',
           "pelvicas_volumen"=>$p->pelvis_volumen,
           "pelvicas_volumen_txt"=>$p->pelvis_volumen_txt??'',
           "pelvicas_articular"=>$p->pelvis_articular,
           "pelvicas_articular_txt"=>$p->pelvis_articular_txt??'',
           "pelvicas_sensibilidad"=>$p->pelvis_sensibilidad,
           "pelvicas_sensibilidad_txt"=>$p->pelvis_sensibilidad_txt??'',
           "pelvicas_osteo"=>$p->pelvis_osteotendino,
           "pelvicas_osteo_txt"=>$p->pelvis_osteotendino_txt??'',
        ]);
       return $rowAffect;
     }




}


