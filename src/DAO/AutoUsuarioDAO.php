<?php
namespace App\DAO;
use App\Entity\Accion;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AutoUsuarioDAO {

    public function __construct(){}

    public static function getAutoUsuario($p){

      /*
            SELECT
	            autos_usuario.id_auto_usuario,
 	            autos_usuario.tipo,
 	            autos_usuario.marca,
 	            autos_usuario.modelo,
 	            autos_usuario.color,
 	            autos_usuario.placas,
 	            autos_usuario.tag,
 	            autos_usuario.repuve_tag,
 	            autos_usuario.fecha_registro,
 	            autos_usuario.estatus,
 	            socios.posicion,
 	            persona.nombre,
 	            persona.apellido_paterno,
 	            persona.apellido_materno,
 	            CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_socio,
 	            CONCAT(accion_origen.numero_accion,CASE accion_origen.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_inicial
            FROM autos_usuario
            INNER JOIN socios ON autos_usuario.cve_socio=socios.cve_socio
            INNER JOIN persona ON socios.cve_persona=persona.cve_persona
            LEFT JOIN acciones ON socios.cve_accion=acciones.cve_accion
            INNER JOIN acciones AS accion_origen ON autos_usuario.cve_accion=accion_origen.cve_accion            
      */

        $acciones= DB::table("autos_usuario")
        ->join("socios","autos_usuario.cve_socio","socios.cve_socio")
        ->join("persona","socios.cve_persona","persona.cve_persona")
        ->leftJoin("acciones","socios.cve_accion","acciones.cve_accion")
        ->join("acciones AS accion_origen","autos_usuario.cve_accion","accion_origen.cve_accion")
        ->select(
               "autos_usuario.id_auto_usuario",
               "autos_usuario.tipo",
               "autos_usuario.marca",
               "autos_usuario.modelo",
               "autos_usuario.color",
               "autos_usuario.placas",
               "autos_usuario.tag",
               "autos_usuario.repuve_tag",
               "autos_usuario.fecha_registro",
               "autos_usuario.estatus",
               "socios.posicion",
               "persona.nombre",
               "persona.apellido_paterno",
               "persona.apellido_materno")
        ->SelectRaw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_socio")
        ->SelectRaw("CONCAT(accion_origen.numero_accion,CASE accion_origen.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_inicial");
            
        if($p->numero_accion ?? false){ $acciones->where("acc.numero_accion",$p->numero_accion);}

        if(is_numeric($p->clasificacion ?? false )){ $acciones->where("acc.clasificacion",$p->clasificacion);}

        if($p->tipo_accion ?? false){$acciones->where("acc.cve_tipo_accion",$p->tipo_accion);}

        if($p->estatus ?? false){$acciones->where("acc.estatus",$p->cve_tipo_accion);}

        return $acciones->get();

   }

   public static function bajaAutoUsuario($id)
   {
      return DB::table("autos_usuario")->where("id_auto_usuario",$id)->update(["estatus"=>2,"fecha_baja"=>Carbon::now()]);
   }

   public static function changeColorAutoUsuario($id,$color)
   {
      return DB::table("autos_usuario")->where("id_auto_usuario",$id)->update(["color"=>$color]);
   }

   public static function changePlacasAutoUsuario($id,$placas)
   {
      return DB::table("autos_usuario")->where("id_auto_usuario",$id)->update(["placas"=>$placas]);
   }

}