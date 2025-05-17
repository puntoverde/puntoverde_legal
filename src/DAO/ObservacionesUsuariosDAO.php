<?php
namespace App\DAO;
use App\Entity\UsuarioObservacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class ObservacionesUsuariosDAO {

    public function __construct(){}

    public static function getAcciones($numero_accion,$clasificacion){

        $dueno= DB::table("acciones")
        ->join("dueno" , "acciones.cve_dueno","dueno.cve_dueno")
        ->join("persona" , "dueno.cve_persona","persona.cve_persona")
        ->leftJoin("usuario_observacion" , "persona.cve_persona","usuario_observacion.cve_persona")
        ->select("acciones.cve_accion","persona.cve_persona",DB::raw("'' AS posicion"),DB::raw("0 AS socio"))
        ->SelectRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS nombre")
        ->SelectRaw("IFNULL(GROUP_CONCAT(usuario_observacion.id),0) n_observacion")         
        ->where("numero_accion",$numero_accion)
        ->where("clasificacion",$clasificacion)
        ->groupBy("persona.cve_persona");

        $socios= DB::table("acciones")
        ->join("socios" , "acciones.cve_accion","socios.cve_accion")
        ->join("persona" , "socios.cve_persona","persona.cve_persona")
        ->leftJoin("usuario_observacion" , "persona.cve_persona","usuario_observacion.cve_persona")
        ->select("acciones.cve_accion","persona.cve_persona","socios.posicion",DB::raw("1 AS socio"))
        ->SelectRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS nombre")
        ->SelectRaw("IFNULL(GROUP_CONCAT(usuario_observacion.id),0) n_observacion")         
        ->where("numero_accion",$numero_accion)
        ->where("clasificacion",$clasificacion)
        ->groupBy("persona.cve_persona");
                    
        return $dueno->union($socios)->get();

   }

   public static function getUsuariosByName($search){

      $duenos=DB::table("persona")
      ->join("dueno" , "persona.cve_persona","dueno.cve_persona")
      ->leftJoin("acciones" , "dueno.cve_dueno","acciones.cve_dueno")
      ->leftJoin("usuario_observacion" , "persona.cve_persona","usuario_observacion.cve_persona")
      ->whereRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) LIKE ?",["%".$search."%"])
      ->groupBy("persona.cve_persona")
      ->select("persona.cve_persona",DB::raw("'' AS posicion"),DB::raw("0 AS socio"))
      ->selectRaw("IFNULL(CONCAT(numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END),'NA') AS accion")
      ->selectRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS nombre")
      ->selectRaw("IFNULL(GROUP_CONCAT(usuario_observacion.id),0) n_observacion");

      $socios=DB::table("persona")
      ->join("socios" , "persona.cve_persona","socios.cve_persona")
      ->leftJoin("acciones" , "socios.cve_accion","acciones.cve_accion")
      ->leftJoin("usuario_observacion" , "persona.cve_persona","usuario_observacion.cve_persona")
      ->whereRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) LIKE ?",["%".$search."%"])
      ->groupBy("persona.cve_persona")
      ->select("persona.cve_persona","socios.posicion",DB::raw("1 AS socio"))
      ->selectRaw("IFNULL(CONCAT(numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END),'NA') AS accion")
      ->selectRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS nombre")
      ->selectRaw("IFNULL(GROUP_CONCAT(usuario_observacion.id),0) n_observacion");

      return $duenos->union($socios)->get();
 }

 public static function showObservaciones($id)
 {   
    return UsuarioObservacion::join("persona" , "usuario_observacion.usuario_realiza","persona.cve_persona")
    ->where("usuario_observacion.cve_persona",$id)
    ->select("id","observacion","fecha")
    ->selectRaw("CONCAT_WS(' ',nombre,apellido_paterno,apellido_materno) AS redacto")
    ->get();
 }

   public static function crearObservacion($p){    
      
      $usuario_observacion=new UsuarioObservacion();
      if($p->cve_persona??0)$usuario_observacion->cve_persona=$p->cve_persona;
      if($p->cve_accion??false)$usuario_observacion->cve_accion=$p->cve_accion;
      if($p->observacion??false)$usuario_observacion->observacion=$p->observacion;
      if($p->usuario_realiza??false)$usuario_observacion->usuario_realiza=$p->usuario_realiza;
      $usuario_observacion->fecha=Carbon::now()->format("Y-m-d");
      $usuario_observacion->save();

      $cve_socio=DB::table("socios")->where("cve_persona",$p->cve_persona)->value("cve_socio");

      var_dump($cve_socio);

      if($cve_socio)DB::table("huella")->where("cveSocio",$cve_socio)->delete();

      return $usuario_observacion->id;
   }

  
}