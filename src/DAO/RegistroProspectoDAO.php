<?php

namespace App\DAO;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegistroProspectoDAO
{

   public function __construct()
   {
   }

   public static function getAccion($p)
   {

       return DB::table('acciones')        
         ->leftJoin('dueno', 'acciones.cve_dueno', 'dueno.cve_dueno')
         ->leftJoin('persona', 'dueno.cve_persona', 'persona.cve_persona')
         ->leftjoin('tipo_accion','acciones.cve_tipo_accion','tipo_accion.cve_tipo_accion')
         ->select('acciones.cve_accion','acciones.estatus','tipo_accion.nombre AS tipo','tipo_accion.cve_tipo_accion')
         ->addSelect(DB::raw("CONCAT_WS(' ',persona.nombre,apellido_paterno,apellido_materno) AS dueno"))
         ->where('numero_accion', $p->numero_accion)
         ->where('clasificacion', $p->clasificacion)
         ->first();    
   }

   public static function saveSolicitud($p)
   {
      $clave=substr(md5(time()), 0, 6);
      $folio=DB::connection("mysql_pros")->table("solicitud")->max("id_solicitud");
      $fecha=Carbon::now();
      $cve_solicitud= DB::connection("mysql_pros")->table("solicitud")->insertGetId([
         "interesado"=>$p->interesado,
         "correo"=>$p->correo,
         "folio"=>$folio+1,
         "clave"=>strtoupper($clave),
         "fecha_registro"=>$fecha,
         "tipo"=>$p->tipo,
         "cantidad_usuarios"=>$p->cantidad_usuarios,
         "registra"=>1,
         "estatus"=>1,
         "terminos_condiciones"=>0,
         "cve_accion"=>$p->cve_accion,
         "accion"=>$p->accion        
      ]);
      return ["id"=>$cve_solicitud,"folio"=>$folio+1,"clave"=>$clave,"fecha"=>$fecha];
   }

   
   public static function getSolicitudes($p)
   {

      /*
SELECT 
solicitud.folio,
solicitud.accion,
persona.id_solicitud,
persona.id_persona,
persona.nombre,
persona.paterno,
persona.materno,
persona.genero,
persona.fecha_nacimiento,
persona.estado_civil,
persona.curp,
persona.rfc,
persona.nacionalidad,
parentescos.nombre AS parentesco,
direcciones.calle,
direcciones.numero_int,
direcciones.numero_ext,
direcciones.cp,
colonia.nombre AS name_colonia,
municipio.nombre AS name_municipio,
estado.nombre AS name_estado,
IF(direcciones.id_direccion>0,TRUE,FALSE) AS complete_data,
persona.estatus
FROM solicitud
INNER JOIN  persona on solicitud.id_solicitud=persona.id_solicitud
INNER JOIN parentescos ON persona.id_parentesco=parentescos.cve_parentesco
LEFT JOIN direcciones ON persona.id_direccion=direcciones.id_direccion
LEFT JOIN colonia ON direcciones.id_colonia=colonia.cve_colonia
LEFT JOIN municipio ON colonia.cve_municipio=municipio.cve_municipio
LEFT JOIN estado ON municipio.cve_estado=estado.cve_estado 
 */

        $query= DB::connection("mysql_pros")
        ->table("solicitud")
        ->join("persona" , "solicitud.id_solicitud","persona.id_solicitud")
        ->join("parentescos" , "persona.id_parentesco","parentescos.cve_parentesco")
        ->leftJoin("direcciones" , "persona.id_direccion","direcciones.id_direccion")
        ->leftJoin("colonia", "direcciones.id_colonia","colonia.cve_colonia")
        ->leftJoin("municipio" , "colonia.cve_municipio","municipio.cve_municipio")
        ->leftJoin("estado" , "municipio.cve_estado","estado.cve_estado")
        ->select(
        "solicitud.folio",
        "solicitud.accion",
        "persona.id_solicitud",
        "persona.id_persona",
        "persona.nombre",
        "persona.paterno",
        "persona.materno",
        "persona.genero",
        "persona.fecha_nacimiento",
        "persona.estado_civil",
        "persona.curp",
        "persona.rfc",
        "persona.nacionalidad",
        "parentescos.nombre AS parentesco",
        "persona.estatus")
        ->addSelect(
         "direcciones.calle",
         "direcciones.numero_int",
         "direcciones.numero_ext",
         "direcciones.cp",
         "colonia.nombre AS name_colonia",
         "municipio.nombre AS name_municipio",
         "estado.nombre AS name_estado")
        ->selectRaw("IF(direcciones.id_direccion>0,TRUE,FALSE) AS complete_data");
        
        if($p->accion??false)$query->where("solicitud.accion",$p->accion);
        if($p->nombre??false)$query->where("persona.nombre","like","%".strtoupper($p->nombre)."%");
        if($p->paterno??false)$query->where("persona.paterno","like","%".strtoupper($p->paterno)."%");
        if($p->materno??false)$query->where("persona.materno","like","%".strtoupper($p->materno)."%");
        if($p->genero??false)$query->where("persona.genero",$p->genero);
        if($p->edad??false)$query->where("persona.fecha_nacimiento",$p->edad);
        if($p->estado_civil??false)$query->where("persona.estado_civil",$p->estado_civil);
        if($p->curp??false)$query->where("persona.curp",$p->curp);
        if($p->rfc??false)$query->where("persona.rfc",$p->rfc);
        if($p->parentesco??false)$query->where("parentescos.nombre",$p->parentesco);
        if($p->estatus??false)$query->where("persona.estatus",$p->estatus);

        return $query->get();
   }













   public static function getDuenos($id)
   {

         $anteriores_duenos=DB::table('acciones_historico')        
         ->join('dueno', 'acciones_historico.cve_dueno', 'dueno.cve_dueno')
         ->join('persona', 'dueno.cve_persona', 'persona.cve_persona')
         ->select('acciones_historico.cve_tipo_accion','acciones_historico.fecha_alta')
         ->addSelect('acciones_historico.fecha_baja','acciones_historico.fecha_adquisicion')
         ->addSelect('acciones_historico.estatus_anterior','acciones_historico.estatus_actual')
         ->addSelect('acciones_historico.observacion')
         ->addSelect(DB::raw("CONCAT_WS(' ',nombre,apellido_paterno,apellido_materno) AS nombre"))
         ->where('acciones_historico.cve_accion', $id)
         ->groupBy('dueno.cve_dueno');
         
         $dueno_actual=DB::table('acciones')
         ->join('dueno', 'acciones.cve_dueno', 'dueno.cve_dueno')
         ->join('persona', 'dueno.cve_persona', 'persona.cve_persona')
         ->select('acciones.cve_tipo_accion','acciones.fecha_alta')
         ->addSelect('acciones.fecha_baja','acciones.fecha_adquisicion')
         ->addSelect('acciones.estatus','acciones.estatus')
         ->addSelect('acciones.motivo')
         ->addSelect(DB::raw("CONCAT_WS(' ',nombre,apellido_paterno,apellido_materno) AS nombre"))
         ->where('acciones.cve_accion', $id)
         ->groupBy('dueno.cve_dueno');

         return $anteriores_duenos->union($dueno_actual)->get();
   }

   public static function getSocios($id)
   {

      return DB::table('socio_accion')
         ->join('socios', 'socio_accion.cve_socio', 'socios.cve_socio')
         ->join('persona', 'socios.cve_persona', 'persona.cve_persona')
         ->join('acciones', 'socio_accion.cve_accion', 'acciones.cve_accion')
         ->select('socio_accion.fecha_alta', 'socio_accion.fecha_baja','socio_accion.fecha_hora_movimiento')
         ->addSelect('socio_accion.movimiento','socios.estatus AS estatus_socio')
         ->addSelect('socio_accion.antiguedad','socio_accion.estatus')
         ->addSelect(DB::raw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS socio"))
         ->addSelect(DB::raw("CONCAT(acciones.numero_accion, CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END,socios.posicion) AS nip"))
         ->where('socio_accion.cve_accion', $id)
         ->get();
   }

   public static function getCargos($id)
   {

      return DB::table('cargo')
      ->join('descuento','cargo.cve_cargo','descuento.cve_cargo')
      ->leftJoin('pago','cargo.idpago','pago.idpago')
      ->select('periodo','concepto','cargo.total AS cargo_total','folio')
      ->addSelect(DB::raw("ROUND(IFNULL(descuento.monto,0),2) AS monto_descuento"))
      ->addSelect(DB::raw("ROUND(cargo.total-IFNULL(descuento.monto,0),2) AS monto_total"))
      ->where('cve_accion', $id)
      ->orderByRaw("SUBSTRING_INDEX(periodo,'-',-1)")
      ->orderByRaw("SUBSTRING_INDEX(periodo,'-',1)")
      ->get();
   }

   public static function getTipoAndEstatus($id)
   {

      $estatus_accion= DB::table('acciones_historico')
      ->select('estatus_actual','estatus_anterior','fecha_modificacion','observacion')      
      ->where('cve_accion', $id)
      ->where('observacion','Cambio de estatus')
      ->get();

      $tipo_accion=DB::table('acciones_historico')
      ->leftJoin('tipo_accion','acciones_historico.cve_tipo_accion','tipo_accion.cve_tipo_accion')
      ->select('nombre','fecha_modificacion','observacion')      
      ->where('cve_accion', $id)
      ->where('observacion','Cambio de tipo de accion')
      ->get();

      return ["estatus_accion"=>$estatus_accion,"tipo_accion"=>$tipo_accion];
   }


}


