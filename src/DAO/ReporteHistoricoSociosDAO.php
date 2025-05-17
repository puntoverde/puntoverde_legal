<?php

namespace App\DAO;

use App\Entity\Locker;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ReporteHistoricoSociosDAO
{

   public function __construct()
   {
   }

   public static function getSocio($nombre)
   {
      /*SELECT cve_socio,CONCAT_WS(' ',nombre,apellido_paterno,apellido_materno) AS socio FROM socios
      INNER JOIN persona ON socios.cve_persona=persona.cve_persona
      WHERE CONCAT_WS(' ',nombre,apellido_paterno,apellido_materno) LIKE '%marco%'*/

       return DB::table('socios')        
         ->leftJoin('persona', 'socios.cve_persona', 'persona.cve_persona')
         ->select('cve_socio')
         ->addSelect(DB::raw("CONCAT_WS(' ',persona.nombre,apellido_paterno,apellido_materno) AS socio"))
         ->whereRaw("CONCAT_WS(' ',nombre,apellido_paterno,apellido_materno) LIKE ?", '%'.$nombre.'%')         
         ->get();    
   }

   public static function getHistorico($id)
   {
         /**SELECT
            CONCAT(acciones.numero_accion,CASE clasificacion when 1 then 'A' when 2 then 'B' WHEN 3 then 'C' ELSE '' END ) AS accion ,
            socio_accion.nip,
            socio_accion.fecha_alta,
            socio_accion.fecha_baja,
            socio_accion.estatus,
            socio_accion.fecha_hora_movimiento,
            socio_accion.movimiento
            FROM socio_accion
            INNER JOIN acciones ON socio_accion.cve_accion=acciones.cve_accion */

       return DB::table('socio_accion')        
         ->join('acciones', 'socio_accion.cve_accion', 'acciones.cve_accion')
         ->select('socio_accion.nip',
         'socio_accion.fecha_alta',
         'socio_accion.fecha_baja',
         'socio_accion.estatus',
         'socio_accion.fecha_hora_movimiento',
         'socio_accion.movimiento')
         ->selectRaw("CONCAT(acciones.numero_accion,CASE clasificacion when 1 then 'A' when 2 then 'B' WHEN 3 then 'C' ELSE '' END ) AS accion")
         ->where('socio_accion.cve_socio', $id)
         ->get();    
   }

   public static function getSocioDatos($id)
   {
      /*SELECT CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno),persona.rfc,
         direccion.calle,direccion.numero_exterior,direccion.numero_interior,colonia.nombre,colonia.cp,
         municipio.nombre AS nom_municipio,estado.nombre AS nom_estado,pais.nombre AS nom_pais,
         socios.celular,socios.telefono,socios.correo_electronico
          FROM socios 
         INNER JOIN persona ON socios.cve_persona=persona.cve_persona
         INNER JOIN direccion ON socios.cve_direccion=direccion.cve_direccion
         INNER JOIN colonia ON direccion.cve_colonia=colonia.cve_colonia
         INNER JOIN municipio ON colonia.cve_municipio=municipio.cve_municipio
         INNER JOIN estado ON municipio.cve_estado=estado.cve_estado
         INNER JOIN pais ON estado.cve_pais=pais.cve_pais
         WHERE socios.cve_socio=10*/

      return DB::table('socios')        
         ->join('persona', 'socios.cve_persona', 'persona.cve_persona')
         ->join('direccion', 'socios.cve_direccion', 'direccion.cve_direccion')
         ->join('colonia', 'direccion.cve_colonia','colonia.cve_colonia')
         ->join('municipio', 'colonia.cve_municipio','municipio.cve_municipio')
         ->join('estado', 'municipio.cve_estado','estado.cve_estado')
         ->join('pais', 'estado.cve_pais','pais.cve_pais')
         ->select('persona.rfc','persona.curp','direccion.calle','direccion.numero_exterior','direccion.numero_interior','colonia.nombre','colonia.cp')
         ->addSelect('municipio.nombre AS nom_municipio','estado.nombre AS nom_estado','pais.nombre AS nom_pais','socios.celular','socios.telefono','socios.correo_electronico')
         ->addSelect(DB::raw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS socio"))                
         ->where('socios.cve_socio',$id)
         ->first();    
   }

   public static function setHistorico($p)
   {

      $cve_accion=DB::table('acciones')->where('numero_accion',$p->numero_accion)->where('clasificacion',$p->clasificacion)->value('cve_accion');
      DB::table('socio_accion')->where('cve_socio',$p->cve_socio)->update(["cve_accion"=>$cve_accion]);
   }



}
