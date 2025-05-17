<?php

namespace App\DAO;

use App\Entity\Locker;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class LockerDAO
{

   public function __construct()
   {
   }

   public static function crearLocker($p)
   {

      try{
        return DB::table("locker")->insertGetId([
           "propietario"=>$p->propietario,
         //   "rentador"=>$p->rentador,
           "tipo"=>$p->tipo,
           "estado"=>$p->estado,
           "ubicacion"=>$p->ubicacion,
           "numero_locker"=>$p->numero_locker,
           "observaciones"=>"nuevo lockers desde catalogo",
           "estatus"=>1,
         //   "numero_locker_anterior"=>$p->numero_locker_anterior,
         //   "cve_accion"=>$p->cve_accion,
        ]);
      }
      catch(\Exception $e)
      {

      }

   }

   public static function ModificarLocker($id,$p)
   {

    return DB::transaction(function()use($id,$p){
       

         $locker=Locker::find($id);
         $locker->propietario=$p->propietario;
         $locker->tipo=$p->tipo;
         $locker->estado=$p->estado;
         $locker->ubicacion=$p->ubicacion;
         $locker->numero_locker=$p->numero_locker;
         $locker->save();

         DB::table("locker_historico_propietario")->insert([
            "id_locker"=>$id,
            "id_persona"=>$locker->rentador,
            "id_persona_renta"=>$p->propietario,
            "id_ubicacion"=>$p->ubicacion,
            "numero_locker"=>$p->numero_locker,
            "fecha"=>Carbon::now(),
            "descripion"=>"Modificacion de locker"
         ]);

         return $locker->cve_locker;

      }); 
   }

   public static function getLockerById($id)
   {
      return DB::table("locker")
      ->join("edificios","locker.ubicacion","edificios.cve_edificio")
      ->select(
         "locker.cve_locker",
         "locker.propietario",
         "locker.ubicacion",
         "locker.cve_accion",
         "locker.tipo",
         "locker.estado",
         "locker.numero_locker",
         "locker.estatus")
      ->where("locker.cve_locker",$id)
      ->first();
   }

   public static function getListaLockers($p)
   {
      /*
         SELECT 
	            locker.cve_locker,
               locker.tipo ,
               locker.estado,
               locker.ubicacion,
               locker.numero_locker,
               locker.estatus,
               locker.numero_locker_anterior,
               locker.cve_accion,
	            prop.nombre AS nombre_prop,
               prop.apellido_paterno AS paterno_prop,
               prop.apellido_materno AS materno_prop,
               rent.nombre AS nombre_rent,
               rent.apellido_paterno AS paterno_rent,
               rent.apellido_materno AS materno_rent,
               edificios.nombre AS ubicacion_name
         FROM locker
         LEFT JOIN persona AS prop ON locker.propietario=prop.cve_persona
         LEFT JOIN persona AS rent ON locker.rentador=rent.cve_persona
         INNER JOIN edificios ON locker.ubicacion=edificios.cve_edificio
       */

      $query=DB::table("locker")
      ->leftJoin("persona AS prop" , "locker.propietario","prop.cve_persona")
      ->leftJoin("persona AS rent" , "locker.rentador","rent.cve_persona")
      ->join("edificios" , "locker.ubicacion","edificios.cve_edificio")
      ->select(
               "locker.cve_locker",
               "locker.tipo",
               "locker.estado",
               "locker.ubicacion",
               "locker.numero_locker",
               "locker.estatus",
               "locker.numero_locker_anterior",
               "locker.cve_accion",
	            "prop.nombre AS nombre_prop",
               "prop.apellido_paterno AS paterno_prop",
               "prop.apellido_materno AS materno_prop",
               "rent.nombre AS nombre_rent",
               "rent.apellido_paterno AS paterno_rent",
               "rent.apellido_materno AS materno_rent",
               "edificios.nombre AS ubicacion_name"
      );

      if($p->num_locker??false){
         $query->where("locker.numero_locker",$p->num_locker);
      }

      if($p->tipo??false)
      {
         $query->whereRaw("CAST(locker.tipo AS UNSIGNED)=?",[$p->tipo]);
      }

      if($p->estado??false)
      {
         $query->whereRaw("CAST(locker.estado AS UNSIGNED)=?",[$p->estado]);
      }


      return $query->get();
   }

   public static function getLockers($p)
   {

      /*
               SELECT 
                     locker.cve_locker,locker.tipo,locker.ubicacion,locker.numero_locker,
                     CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS propietario,
                     CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_propietario,
                     CONCAT_WS(' ',persona_1.nombre,persona_1.apellido_paterno,persona_1.apellido_materno) AS rentador,
                     CONCAT(acciones_1.numero_accion,CASE acciones_1.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_rentador,
                     IFNULL(dueno.cve_dueno,socios.cve_socio) AS dueno_or_socio_cve,
                     IF(dueno.cve_dueno IS NOT NULL,'dueno','socio') AS propietario_is
               FROM locker
               LEFT JOIN persona ON locker.propietario=persona.cve_persona
               LEFT JOIN dueno ON locker.propietario=dueno.cve_persona
               LEFT JOIN socios ON locker.propietario=socios.cve_persona
               LEFT JOIN acciones ON dueno.cve_dueno=acciones.cve_dueno OR socios.cve_accion=acciones.cve_accion
               LEFT JOIN persona AS persona_1 ON locker.rentador=persona_1.cve_persona
               LEFT JOIN socios AS socios_1 ON locker.rentador=socios_1.cve_persona
               LEFT JOIN acciones AS acciones_1 ON socios_1.cve_accion=acciones_1.cve_accion
               WHERE locker.numero_locker = ? 
               AND ((acciones.numero_accion = ? AND acciones.clasificacion = ?) OR (acciones_1.numero_accion = ? AND acciones_1.clasificacion = ?)) 
               AND (CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) LIKE ? OR CONCAT_WS(' ',persona_1.nombre,persona_1.apellido_paterno,persona_1.apellido_materno) LIKE ?)

       */

      /** WHERE acciones.numero_accion=2 AND acciones.clasificacion=3 OR acciones_1.numero_accion=178 AND acciones_1.clasificacion=0 */

      // subquery para sacar la permuta       
      $permuta=DB::table("locker AS lock")
      ->join("persona" , "lock.propietario","persona.cve_persona")
      ->join("edificios AS edifi","lock.ubicacion","edifi.cve_edificio")
      ->join("locker_permuta" , "lock.cve_locker","locker_permuta.cve_locker_dos")
      ->whereColumn("cve_locker_uno","locker.cve_locker")
      ->selectRaw("CONCAT('# locker:',lock.numero_locker,', ubicacion: ',edifi.nombre,', dueño:',CONCAT_WS(' ',persona.nombre,apellido_paterno,apellido_materno))");

      $lockers = Locker::join("edificios", "locker.ubicacion", "edificios.cve_edificio")
         ->leftJoin('persona', 'propietario', 'persona.cve_persona')
         ->leftJoin('dueno', 'propietario', 'dueno.cve_persona')
         ->leftJoin('socios', 'propietario', 'socios.cve_persona')
         ->leftJoin('acciones', function ($join) {
            $join->on('dueno.cve_dueno', 'acciones.cve_dueno')->orOn('socios.cve_accion', 'acciones.cve_accion');
         });

      $lockers->leftJoin('persona AS persona_1', 'rentador', 'persona_1.cve_persona')
         ->leftJoin('socios AS socios_1', 'rentador', 'socios_1.cve_persona')
         ->leftJoin('acciones AS acciones_1', 'socios_1.cve_accion', 'acciones_1.cve_accion');

      $lockers->select('locker.cve_locker', 'locker.tipo', 'locker.ubicacion', 'locker.numero_locker', 'locker.estado', 'edificios.nombre AS ubicacion_edificio');
      $lockers->addSelect(DB::raw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS propietario"));
      $lockers->addSelect(DB::raw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_propietario"));
      $lockers->addSelect(DB::raw("CONCAT_WS(' ',persona_1.nombre,persona_1.apellido_paterno,persona_1.apellido_materno) AS rentador"));
      $lockers->addSelect(DB::raw("CONCAT(acciones_1.numero_accion,CASE acciones_1.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_rentador"));
      $lockers->addSelect(DB::raw("IFNULL(dueno.cve_dueno,socios.cve_socio) AS dueno_or_socio_cve"));
      $lockers->addSelect(DB::raw("IF(dueno.cve_dueno IS NOT NULL,'dueno','socio') AS propietario_is"));
      $lockers->addSelect("locker.numero_locker_anterior");
      $lockers->addSelect(["permuta"=>$permuta]);

      if ($p->numero_locker ?? false) $lockers->where('locker.numero_locker', strtoupper($p->numero_locker));

      if (($p->numero_accion ?? false) && is_numeric($p->clasificacion ?? false)) {

         $lockers->where(function ($query) use ($p) {

            $query->where(function ($query) use ($p) {
               $query->where('acciones.numero_accion', $p->numero_accion)->where('acciones.clasificacion', $p->clasificacion);
            });

            $query->orWhere(function ($query) use ($p) {
               $query->where('acciones_1.numero_accion', $p->numero_accion)->where('acciones_1.clasificacion', $p->clasificacion);
            });
         });
      }

      if ($p->nombre ?? false) {

         $lockers->where(function ($query) use ($p) {

            $query->whereRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) LIKE ?", ['%' . $p->nombre . '%']);
            $query->orWhereRaw("CONCAT_WS(' ',persona_1.nombre,persona_1.apellido_paterno,persona_1.apellido_materno) LIKE ?", ['%' . $p->nombre . '%']);
         });
      }

      if ($p->ubicacion ?? false) {
         $lockers->where('edificios.cve_edificio', $p->ubicacion);
      }

      // return $lockers->toSql();
      return $lockers->get();
   }

   public static function getHistoricoLocker($id)
   {
      /*
         SELECT asignacion_locker.fecha_incio,asignacion_locker.fecha_fin,
         CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS rentador,
         if(CURDATE() BETWEEN asignacion_locker.fecha_incio AND asignacion_locker.fecha_fin,1,0) AS activo,
         cargo.total,IFNULL(cargo.idpago,0) AS pagado
         FROM asignacion_locker
         INNER JOIN persona ON asignacion_locker.cve_persona=persona.cve_persona
         LEFT JOIN cargo ON asignacion_locker.cve_cargo=cargo.cve_cargo AND cargo.cve_cargo=?
         WHERE asignacion_locker.cve_locker=1

       */

      $lockers_asignados = DB::table('asignacion_locker')
         ->join('persona', 'asignacion_locker.cve_persona', 'persona.cve_persona')
         ->leftJoin('cargo', 'asignacion_locker.cve_cargo', 'cargo.cve_cargo')
         ->leftJoin('pago', 'cargo.idpago', 'pago.idpago')
         ->select('asignacion_locker.cve_asignacion_locker', 'asignacion_locker.fecha_incio', 'asignacion_locker.fecha_fin', 'pago.folio', 'persona.cve_persona')
         ->addSelect('asignacion_locker.folio AS folio_con', 'asignacion_locker.documento', 'asignacion_locker.observaciones', 'asignacion_locker.estatus', 'fecha_cancelacion')
         ->addSelect(DB::raw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS rentador"))
         ->addSelect(DB::raw('if(CURDATE() BETWEEN asignacion_locker.fecha_incio AND asignacion_locker.fecha_fin,1,0) && asignacion_locker.estatus AS activo'))
         ->addSelect(DB::raw('IFNULL(cargo.total,0) AS total'), DB::raw('IFNULL(cargo.idpago,0) AS pagado'))
         ->where('asignacion_locker.cve_locker', $id)
         ->orderBy('asignacion_locker.fecha_incio', 'desc');

      return $lockers_asignados->get();
   }

   public static function asignarLocker($id, $p)
   {
      return DB::transaction(function () use ($id, $p) {

         $rentado = DB::table('asignacion_locker')->whereRaw("CURDATE() BETWEEN asignacion_locker.fecha_incio AND asignacion_locker.fecha_fin")->where("estatus", 1)->where("cve_locker", $id)->count();

         if ($rentado == 0) {
            $subtotal = ($p->total / 116) * 100;
            $iva = ($subtotal * .16);
            $periodo = date('m-Y');
            $cve_cargo = DB::table('cargo')->insertGetId(
               [
                  'cve_accion' => $p->cve_accion, 'cve_cuota' => 40, 'cve_persona' => $p->cve_persona, 'concepto' => $p->concepto,
                  'total' => $p->total, 'subtotal' => $subtotal, 'iva' => $iva, 'cantidad' => 1, 'periodo' => $periodo, 'responsable_carga' => 0,
                  'fecha_cargo' => Carbon::now(), 'recargo' => 0, 'estatus' => 1
               ]
            );

            $locker = Locker::find($id);
            $locker->rentador = $p->cve_persona;
            $locker->rentadores()
               ->attach(
                  $p->cve_persona,
                  [
                     'cve_cargo' => $cve_cargo,
                     'fecha_incio' => $p->fecha_inicio,
                     'fecha_fin' => $p->fecha_fin,
                     'tipo' => $p->tipo,
                     'estatus' => 1
                  ]
               );
            $locker->save();

            return 1;
         } else {
            return ["msg" => "El Locker ya esta rentado...."];
         }
      });
   }

   public static function cancelarRentaLocker($id, $p)
   {
      $locker = Locker::find($id);
      $locker->rentadores()->updateExistingPivot($p->cve_persona, ['estatus' => 0, 'fecha_cancelacion' => Carbon::now(),"motivo_cancelacion"=>$p->motivo_cancelacion]);
   }

   public static function getSocios($p)
   {
      /**SELECT 
             persona.cve_persona,acciones.cve_accion,
             CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS rentador,
             CONCAT(acciones.numero_accion, CASE
             acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_propietario
         FROM socios 
         INNER JOIN persona ON socios.cve_persona=persona.cve_persona
         INNER JOIN acciones ON socios.cve_accion=acciones.cve_accion
         WHERE socios.estatus=1 AND socios.cve_accion IS NOT NULL AND acciones.estatus IN(1,2)
         AND CONCAT_WS('
             ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) LIKE '%?%' 
             AND acciones.numero_accion = 175 AND acciones.clasificacion = 0
       */

      $socios = DB::table('socios')
         ->join('persona', 'socios.cve_persona', 'persona.cve_persona')
         ->join('acciones', 'socios.cve_accion', 'acciones.cve_accion')
         ->select('persona.cve_persona', 'acciones.cve_accion')
         ->addSelect(DB::raw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS rentador"))
         ->addSelect(DB::raw("CONCAT(acciones.numero_accion, CASE
       acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion"))
         ->where('socios.estatus', 1)
         ->whereNotNull('socios.cve_accion')
         ->whereIn('acciones.estatus', [1, 2]);

      if ($p->nombre ?? false) {
         $socios->whereRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) LIKE ?", ['%' . $p->nombre . '%']);
      }
      if (($p->numero_accion ?? false) && is_numeric($p->clasificacion ?? false)) {
         $socios->where('acciones.numero_accion', $p->numero_accion)->where('acciones.clasificacion', $p->clasificacion);
      }

      //  return $socios->toSql();
      return $socios->get();
   }

   public static function getCuota()
   {
      return DB::table('cuota')->where('cve_cuota', 40)->select('descripcion', 'cantidad')->first();
   }

   public static function AsignarContrato($id, $p)
   {
      $locker = Locker::find($id);
      $locker->rentadores()
         ->updateExistingPivot($p->cve_persona, ['folio' => $p->folio, 'documento' => $p->documento, 'observaciones' => $p->observaciones]);
   }

   public static function regularizadoNoRegularizado($id, $p)
   {
      try {
         $locker = Locker::find($id);
         $locker->estado = $p->estado;
         $locker->save();
         return 1;
      } catch (\Exception $e) {
         return 0;
      }
   }

   public static function getEdificios()
   {
      try {
         return DB::table("edificios")->where("cve_edificio", ">=", 2006)->where("cve_edificio", "<=", 2012)->get();
      } catch (\Exception $e) {
         return [];
      }
   }

   public static function getListaLockerDisponibles()
   {
      /* 
         SELECT 
               `locker`.`cve_locker`, 
               `locker`.`numero_locker`, 
               `locker`.`propietario`, 
               `locker`.`rentador`, 
               `persona`.`nombre`, 
               `persona`.`apellido_paterno`, 
               `persona`.`apellido_materno`, 
               `socios`.`cve_accion`, 
               CONCAT(numero_accion, CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion, 
               CONCAT(YEAR(NOW()),'-12-31') AS fecha_fin, 
               `edificios`.`nombre` AS `ubicacion`, 
               CURDATE() AS fecha_inicio, 
               (SELECT `cantidad` FROM `cuota` WHERE `cve_cuota` = 40 LIMIT 1) AS `costo`
         FROM `locker`
         inner JOIN `persona` ON `locker`.`rentador` = `persona`.`cve_persona`
         inner JOIN `socios` ON `persona`.`cve_persona` = `socios`.`cve_persona`
         inner JOIN `acciones` ON `socios`.`cve_accion` = `acciones`.`cve_accion`
         INNER JOIN `edificios` ON `locker`.`ubicacion` = `edificios`.`cve_edificio`
         INNER JOIN `asignacion_locker` ON `locker`.`cve_locker` = `asignacion_locker`.`cve_locker`
         WHERE `propietario` = 12981 AND DATE_SUB(CURDATE(), INTERVAL 1 YEAR) BETWEEN asignacion_locker.fecha_incio AND asignacion_locker.fecha_fin AND asignacion_locker.estatus=1
         GROUP BY `locker`.`cve_locker`
      */         

         $costo_cuota=DB::table("cuota")->where("cve_cuota",40)->select("cantidad")->limit(1);

         $lockers_disponibles=Locker::leftJoin("persona","locker.rentador","persona.cve_persona")
         ->join("socios","persona.cve_persona","socios.cve_persona")
         ->join("acciones","socios.cve_accion","acciones.cve_accion")
         ->join("edificios","locker.ubicacion","edificios.cve_edificio")
         ->join("asignacion_locker","locker.cve_locker","asignacion_locker.cve_locker")
         ->where("propietario",12981)
         ->where("asignacion_locker.estatus",1)
         ->whereRaw("DATE_SUB(CURDATE(), INTERVAL 1 YEAR) BETWEEN asignacion_locker.fecha_incio AND asignacion_locker.fecha_fin")
         ->groupBy("locker.cve_locker");
         // ->havingRaw("SUM(IF(CURDATE() BETWEEN asignacion_locker.fecha_incio AND asignacion_locker.fecha_fin AND asignacion_locker.estatus=1,1,0))=0");

         $lockers_disponibles->select("locker.cve_locker","locker.numero_locker","locker.propietario","locker.rentador")
         ->addSelect("persona.nombre","persona.apellido_paterno","persona.apellido_materno","socios.cve_accion")
         ->selectRaw("CONCAT(numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion")
         ->selectRaw("CONCAT(YEAR(NOW()),'-12-31') AS fecha_fin")
         ->addSelect("edificios.nombre AS ubicacion",DB::raw("CURDATE() AS fecha_inicio"))
         ->addSelect(["costo"=>$costo_cuota]);


         return $lockers_disponibles->get();
   }


   public static function asignarLockerMasivo($p)
   {
      dd($p);
      return 0;
      $result=collect();
      return DB::transaction(function () use ($p,$result) {

         foreach($p as $item){
         $item=(object)$item;

         $rentado = DB::table('asignacion_locker')
         ->whereRaw("CURDATE() BETWEEN asignacion_locker.fecha_incio AND asignacion_locker.fecha_fin")
         ->where("estatus", 1)
         ->where("cve_locker", $item->cve_locker)
         ->count();   
            
         if ($rentado == 0) {            
            //se trunca a 3 decimales
            $subtotal=floor((($item->total/1.16)*1000))/1000;
            $iva=floor((($subtotal*.16)*1000))/1000;

            $periodo = date('m-Y');
            $cve_cargo = DB::table('cargo')->insertGetId(
               [
                  'cve_accion' => $item->cve_accion, 'cve_cuota' => 40, 'cve_persona' => $item->cve_persona, 'concepto' => $item->concepto,
                  'total' => $item->total, 'subtotal' => round($subtotal,2), 'iva' => round($iva,2), 'cantidad' => 1, 'periodo' => $periodo, 'responsable_carga' => 0,
                  'fecha_cargo' => Carbon::now(), 'recargo' => 0, 'estatus' => 1
               ]
            );

            $locker = Locker::find($item->cve_locker);
            $locker->rentador = $item->cve_persona;
            $locker->rentadores()
               ->attach(
                  $item->cve_persona,
                  [
                     'cve_cargo' => $cve_cargo,
                     'fecha_incio' => $item->fecha_inicio,
                     'fecha_fin' => $item->fecha_fin,
                     'tipo' => $item->tipo,
                     'estatus' => 1
                  ]
               );
            $locker->save();

            $result->push(["locker_id"=>$item->cve_locker,"locker_name"=>$locker->numero_locker,"cve_cargo"=>$cve_cargo]);
         } else {
            // return ["msg" => "El Locker ya esta rentado...."];
            $locker = Locker::find($item->cve_locker);
            $result->push(["locker_id"=>$item->cve_locker,"locker_name"=>$locker->numero_locker,"cve_cargo"=>0]);
         }
      }//fin for each
      
      return$result;//regresa list de lockers

      });//fin transaction
   }


   public static function EditarAsignacionLocker($id, $p)
   {
      return DB::transaction(function () use ($id, $p) {
           

            $locker = Locker::find($id);
            $locker->rentador = $p->cve_persona;          
            $locker->save();

            $asignacion = DB::table('asignacion_locker')
            ->whereRaw("CURDATE() BETWEEN asignacion_locker.fecha_incio AND asignacion_locker.fecha_fin")
            ->where("estatus", 1)
            ->where("cve_locker", $id)
            ->value("cve_asignacion_locker");

            DB::table("asignacion_locker")->where("cve_asignacion_locker",$asignacion)->update(["cve_persona"=>$p->cve_persona]);

            DB::table("locker_historico_propietario")->insert([
               "id_locker"=>$id,
               "id_persona"=>$locker->propietario,
               "id_persona_renta"=>$p->cve_persona,
               "id_ubicacion"=>$locker->ubicacion,
               "numero_locker"=>$locker->numero_locker,
               "fecha"=>Carbon::now(),
               "descripion"=>"Cambio de rentador"
            ]);

            return $asignacion;
         
      });
   }

   public static function getDuenos($p)
   {
      return DB::transaction(function()use($p){

         /* 
         SELECT * FROM ((SELECT 
                  persona.cve_persona, 
                  acciones.cve_accion, 
                  CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS rentador,
                  GROUP_CONCAT(CONCAT(acciones.numero_accion, CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END)) AS accion,
                  'd' AS tipo FROM dueno
            INNER JOIN persona ON dueno.cve_persona=persona.cve_persona
            LEFT JOIN acciones ON dueno.cve_dueno=acciones.cve_dueno
            WHERE  acciones.numero_accion<=1500 OR acciones.cve_accion IS NULL 
            GROUP BY dueno.cve_dueno ORDER BY trim(persona.nombre),trim(persona.apellido_paterno))

            UNION

            (SELECT 
                  persona.cve_persona, 
                  acciones.cve_accion, 
                  CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS rentador,
                  GROUP_CONCAT(CONCAT(acciones.numero_accion, CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END)) AS accion,
                  's' AS tipo FROM socios 
            INNER JOIN persona ON socios.cve_persona=persona.cve_persona
            LEFT JOIN acciones ON socios.cve_accion=acciones.cve_accion
            WHERE  acciones.numero_accion<=1500 OR acciones.cve_accion IS NULL
            GROUP BY socios.cve_socio ORDER BY trim(persona.nombre),trim(persona.apellido_paterno))) AS t

            SELECT 
                  persona.cve_persona, 
                  acciones.cve_accion, 
                  CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS rentador,
                  CONCAT(acciones.numero_accion, CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion,
                  'd' AS tipo FROM socios 
            INNER JOIN persona ON socios.cve_persona=persona.cve_persona
            LEFT JOIN acciones ON socios.cve_accion=acciones.cve_accion
            WHERE  persona.cve_persona=12981
         */


            $subquery_one=DB::table("dueno")
               ->join("persona" , "dueno.cve_persona","persona.cve_persona")
               ->leftJoin("acciones" , "dueno.cve_dueno","acciones.cve_dueno")
               ->where( "acciones.numero_accion","<=",1500)
               ->orWhereNull("acciones.cve_accion")
               ->groupBy("dueno.cve_dueno")
               ->orderByRaw("trim(persona.nombre),trim(persona.apellido_paterno)")
               ->select("persona.cve_persona","acciones.cve_accion","acciones.numero_accion","acciones.clasificacion")
               ->selectRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS rentador")
               ->selectRaw("GROUP_CONCAT(CONCAT(acciones.numero_accion, CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END)) AS accion")
               ->selectRaw("'d' AS tipo");
        

            $subquery_two=DB::table("socios")
               ->join("persona" , "socios.cve_persona","persona.cve_persona")
               ->leftJoin("acciones" , "socios.cve_accion","acciones.cve_accion")
               ->where("acciones.numero_accion","<=",1500)
               ->orWhereNull("acciones.cve_accion")
               ->groupBy("socios.cve_socio")
               ->orderByRaw("trim(persona.nombre),trim(persona.apellido_paterno)")
               ->select("persona.cve_persona","acciones.cve_accion","acciones.numero_accion","acciones.clasificacion")
               ->selectRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS rentador")
               ->selectRaw("GROUP_CONCAT(CONCAT(acciones.numero_accion, CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END)) AS accion")
               ->selectRaw("'s' AS tipo");

            $subquery_three=DB::table("socios")
               ->join("persona" , "socios.cve_persona","persona.cve_persona")
               ->leftJoin("acciones" , "socios.cve_accion","acciones.cve_accion")
               ->where("persona.cve_persona",12981)
               ->select("persona.cve_persona","acciones.cve_accion","acciones.numero_accion","acciones.clasificacion")
               ->selectRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS rentador")
               ->selectRaw("CONCAT(acciones.numero_accion, CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion")
               ->selectRaw("'d' AS tipo");

            $temporaty_table= DB::table($subquery_one->union($subquery_two)->union($subquery_three));
          
      if ($p->nombre ?? false) {

         $temporaty_table->whereRaw("rentador LIKE ?", ['%' . $p->nombre . '%']);
      }
      if (($p->numero_accion ?? false) && is_numeric($p->clasificacion ?? false)) {
         $temporaty_table->where('acciones.numero_accion', $p->numero_accion)->where('acciones.clasificacion', $p->clasificacion);
      }

      return $temporaty_table->get();
   });
   }

   public static function EditarDuenoLocker($id, $p)
   {
      return DB::transaction(function () use ($id, $p) {
           

            $locker = Locker::find($id);
            $locker->propietario = $p->cve_persona;          
            $locker->save();

            DB::table("locker_historico_propietario")->insert([
               "id_locker"=>$id,
               "id_persona"=>$p->cve_persona,
               "id_persona_renta"=>$locker->rentador,
               "id_ubicacion"=>$locker->ubicacion,
               "numero_locker"=>$locker->numero_locker,
               "fecha"=>Carbon::now(),
               "descripion"=>"Cambio dueño"
            ]);

            return 1;
         
      });
   }

   public static function EditarUltimoNumeroLocker($id, $p)
   {
      return DB::transaction(function () use ($id, $p) {
           

            $locker = Locker::find($id);
            $locker->numero_locker_anterior = $p->numero_locker;          
            $locker->save();
            return 1;
         
      });
   }

   public static function EditarObservacionAsignacion($id, $p)
   {
      return DB::transaction(function () use ($id, $p) {
           

            DB::table("asignacion_locker")->where("cve_asignacion_locker",$id)->update(["observaciones"=>$p->observacion]);
         
      });
   }

   public static function agregar_permuta($p)
   {
      return DB::transaction(function () use ($p) {
            
            if(DB::table("locker_permuta")->where("cve_locker_uno",$p->cve_locker_uno)->exists())
            {
               DB::table("locker_permuta")->where("cve_locker_uno",$p->cve_locker_uno)->update(["cve_locker_dos"=>$p->cve_locker_dos]);  
            }
            else{
               DB::table("locker_permuta")->insert(["cve_locker_uno"=>$p->cve_locker_uno,"cve_locker_dos"=>$p->cve_locker_dos]);
            }

         
      });
   }

   public static function liberaLocker($p)
   {
      $id=DB::table("locker_liberar")
      ->insertGetId([
         "id_locker"=>$p->id_locker,
         "id_persona"=>$p->id_persona,
         "motivo"=>$p->motivo,
         "fecha"=>Carbon::now("America/Mexico_City"),
         "autoriza"=>$p->authoriza,
         "libera"=>$p->realiza   
      ]);
      return $id;
   }

   public static function historicoLiberacion($id)
   {   
      return DB::table("locker_liberar")
      ->join("persona","locker_liberar.id_persona","persona.cve_persona")
      ->select(DB::raw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS nombre"),"motivo","fecha")
      ->where("locker_liberar.id_locker",$id)
      ->get();
   }

   public static function reporteHistoricoLiberacion($p)
   {         
      return DB::table("locker_liberar")
      ->join("persona","locker_liberar.id_persona","persona.cve_persona")
      ->join("locker","locker_liberar.id_locker","locker.cve_locker")
      ->select(DB::raw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS nombre"),"motivo","fecha","locker.numero_locker")      
      ->whereBetween(DB::raw("CONVERT(fecha,date)"),[$p->fechaI,$p->fechaF])
      ->get();
   }

}
