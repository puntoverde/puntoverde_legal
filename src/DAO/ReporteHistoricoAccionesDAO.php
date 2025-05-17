<?php

namespace App\DAO;

use App\Entity\Locker;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ReporteHistoricoAccionesDAO
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
         ->select('acciones.cve_accion','acciones.estatus','tipo_accion.nombre AS tipo')
         ->addSelect(DB::raw("CONCAT_WS(' ',persona.nombre,apellido_paterno,apellido_materno) AS dueno"))
         ->where('numero_accion', $p->numero_accion)
         ->where('clasificacion', $p->clasificacion)
         ->first();    
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
