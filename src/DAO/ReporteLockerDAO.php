<?php

namespace App\DAO;

use App\Entity\Locker;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Query;
use Illuminate\Support\Str;

class ReporteLockerDAO
{

   public function __construct()
   {
   }

   public static function getEdificios()
   {
       return DB::table('edificios')->whereBetween("cve_edificio",[2006,2012])->select('cve_edificio','nombre')->get();
   }

   public static function getReporteLockers($p)
   {
       $lockers=DB::table('locker')
       ->leftJoin('asignacion_locker' ,'locker.cve_locker', 'asignacion_locker.cve_locker')
       ->join('edificios','locker.ubicacion','edificios.cve_edificio')
       ->leftJoin("persona","locker.propietario","persona.cve_persona")
       ->leftJoin("cargo","asignacion_locker.cve_cargo","cargo.cve_cargo")
       ->leftJoin("pago","cargo.idpago","pago.idpago")
       ->select('locker.cve_locker','propietario','rentador','asignacion_locker.tipo','estado','ubicacion','numero_locker')
       ->addSelect('edificios.nombre AS ubicacion_nombre')
       ->addSelect(DB::raw('SUM(IF(CURDATE() BETWEEN fecha_incio AND fecha_fin AND asignacion_locker.estatus=1,1,0)) AS rentado')) 
       ->selectRaw("GROUP_CONCAT(DISTINCT IF(CURDATE() BETWEEN fecha_incio AND fecha_fin AND asignacion_locker.estatus=1,asignacion_locker.fecha_incio,NULL)) AS periodo_inicio")
       ->selectRaw("GROUP_CONCAT(DISTINCT IF(CURDATE() BETWEEN fecha_incio AND fecha_fin AND asignacion_locker.estatus=1,asignacion_locker.fecha_fin,NULL)) AS periodo_fin")
       ->selectRaw("GROUP_CONCAT(DISTINCT IF(CURDATE() BETWEEN fecha_incio AND fecha_fin AND asignacion_locker.estatus=1,cargo.total,NULL)) AS total")
       ->selectRaw("GROUP_CONCAT(DISTINCT IF(CURDATE() BETWEEN fecha_incio AND fecha_fin AND asignacion_locker.estatus=1,pago.folio,NULL)) AS folio")
       ->selectRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno ) AS propietario_nombre")
       ->groupBy('locker.cve_locker');

       if($p->ubicacion ?? false) $lockers->where('locker.ubicacion',$p->ubicacion);
       if(is_numeric($p->renta ?? false)) $lockers->havingRaw('SUM(IF(CURDATE() BETWEEN fecha_incio AND fecha_fin AND asignacion_locker.estatus=1,1,0)) =?',[$p->renta]);
       if($p->propietario ?? false && $p->propietario==1)$lockers->where('propietario',12981);//cuando sean de pv
       if(is_numeric($p->propietario ?? false) && $p->propietario==0)$lockers->where('propietario',"!=",12981);//cuando no sean de pv 
       if($p->estado ?? false) $lockers->where('estado',$p->estado);

       
       return $lockers->get();
      // return Str::replaceArray('?', $lockers->getBindings(), $lockers->toSql());
   }

   public static function getTotales($p)
   {
      $total=DB::table("locker")->count();
      $depv=DB::table("locker")->where("propietario",12981)->count();
      $nodepv=DB::table("locker")->where("propietario","!=",12981)->count();
      
      $ocupados=DB::table("locker")
      ->join('asignacion_locker' ,'locker.cve_locker', 'asignacion_locker.cve_locker')
      ->whereRaw("CURDATE() BETWEEN asignacion_locker.fecha_incio AND asignacion_locker.fecha_fin")     
      ->count();

      $libres=DB::select("SELECT SUM(SUB.LIBRE) AS libres FROM
                               (SELECT 
                                      COUNT(DISTINCT locker.cve_locker) AS LIBRE,
                                      SUM(IF(CURDATE() BETWEEN asignacion_locker.fecha_incio AND asignacion_locker.fecha_fin,1,0)) AS ninguno 
                                FROM locker 
                                LEFT JOIN asignacion_locker ON locker.cve_locker=asignacion_locker.cve_locker  
                                GROUP BY locker.cve_locker) AS SUB
                          WHERE SUB.ninguno=0;");

      return ["total"=>$total,"d_pv"=>$depv,"no_d_pv"=>$nodepv,"libres"=>0,"ocupados"=>$ocupados,"libres"=>intval($libres[0]->libres)];
   }

   public static function getReporteLockersFull($p)
   {
      $total=DB::table("locker")->count();
      $depv=DB::table("locker")->where("propietario",12981)->count();
      $nodepv=DB::table("locker")->where("propietario","!=",12981)->count();
      
      $ocupados=DB::table("locker")
      ->join('asignacion_locker' ,'locker.cve_locker', 'asignacion_locker.cve_locker')
      ->whereRaw("CURDATE() BETWEEN asignacion_locker.fecha_incio AND asignacion_locker.fecha_fin")     
      ->count();

      $libres=DB::select("SELECT SUM(SUB.LIBRE) AS libres FROM
                               (SELECT 
                                      COUNT(DISTINCT locker.cve_locker) AS LIBRE,
                                      SUM(IF(CURDATE() BETWEEN asignacion_locker.fecha_incio AND asignacion_locker.fecha_fin,1,0)) AS ninguno 
                                FROM locker 
                                LEFT JOIN asignacion_locker ON locker.cve_locker=asignacion_locker.cve_locker  
                                GROUP BY locker.cve_locker) AS SUB
                          WHERE SUB.ninguno=0;");

      $query=DB::table("locker")
      ->join('persona','locker.propietario','persona.cve_persona')
      ->join('edificios','locker.ubicacion','edificios.cve_edificio')
      ->leftJoin("asignacion_locker","locker.cve_locker","asignacion_locker.cve_locker")
      ->select("locker.cve_locker","locker.tipo","locker.numero_locker","locker.propietario as id_propietario")
      ->addSelect("persona.nombre AS propietario","edificios.nombre as ubicacion_nombre","locker.estado")
      ->addSelect("locker.rentador","asignacion_locker.cve_persona","asignacion_locker.fecha_incio","asignacion_locker.fecha_fin")
      ->selectRaw("SUM(IF(CURDATE() BETWEEN asignacion_locker.fecha_incio AND asignacion_locker.fecha_fin,1,0)) AS rentado")
      ->groupBy("locker.cve_locker");

      foreach($p->filters??[] as $item)
      {      

      if($item==1)
      {
        
      }

      else if($item==2)
      {
         $query->where("locker.propietario",12981);
      }

      else if($item==3)
      {
         $query->where("locker.propietario","!=",12981);
      }

      if($item==4)
      {
         $query->havingRaw("SUM(IF(CURDATE() BETWEEN asignacion_locker.fecha_incio AND asignacion_locker.fecha_fin,1,0))=0");
      }

      if($item==5)
      {
         
         $query->whereRaw("CURDATE() BETWEEN asignacion_locker.fecha_incio AND asignacion_locker.fecha_fin");
      }
       
      }
      
      return ["total"=>$total,"d_pv"=>$depv,"no_d_pv"=>$nodepv,"libres"=>0,"ocupados"=>$ocupados,"libres"=>intval($libres[0]->libres),"data"=>$query->get()];
      
   }


   public static function getEstadisticasCards($p)
   {
      $periodo=$p->periodo;

      
 
      // rentados
      // SELECT COUNT(asignacion_locker.cve_locker) AS rentados FROM asignacion_locker 
      // INNER JOIN locker ON asignacion_locker.cve_locker=locker.cve_locker
      // INNER JOIN cargo ON asignacion_locker.cve_cargo=cargo.cve_cargo
      // WHERE YEAR(asignacion_locker.fecha_incio)=2024 AND cargo.idpago IS NOT NULL AND locker.propietario=12981
     

      $rentados=DB::table("asignacion_locker")
      ->join("locker" , "asignacion_locker.cve_locker","locker.cve_locker")
      ->join("cargo" , "asignacion_locker.cve_cargo","cargo.cve_cargo")
      ->whereRaw("YEAR(asignacion_locker.fecha_incio)=?",[$periodo-1])
      ->whereNotNull("cargo.idpago")
      ->where("locker.propietario",12981)
      ->count();

      /*
                  ////////////SELECT COUNT(t.cve_locker) AS sin_renovar FROM (
                  ////////////      SELECT 
                  ////////////         asignacion_locker.cve_locker                           
                  ////////////      FROM asignacion_locker 
                  ////////////      WHERE  YEAR(fecha_incio) IN(2023,2024) AND asignacion_locker.estatus=1
                  ////////////      GROUP BY cve_locker 
                  ////////////      HAVING CAST(GROUP_CONCAT(IF(year(asignacion_locker.fecha_incio)=2023 ,1,0),IF(year(asignacion_locker.fecha_incio)=2024 ,1,0) SEPARATOR '') AS SIGNED )=10) 
                  ////////////AS t


                  SELECT COUNT(t.cve_locker) AS sin_renovar FROM (
                        SELECT 
                           asignacion_locker.cve_locker
                        FROM asignacion_locker 
                        INNER JOIN locker ON asignacion_locker.cve_locker=locker.cve_locker
                        INNER JOIN cargo ON  asignacion_locker.cve_cargo=cargo.cve_cargo
                        WHERE  YEAR(fecha_incio) IN(2023,2024) AND cargo.idpago IS NOT NULL AND locker.propietario=12981
                        GROUP BY asignacion_locker.cve_locker
                        HAVING  GROUP_CONCAT(YEAR(fecha_incio))=2023 AND Count(asignacion_locker.cve_locker)=1
                     ) AS t
      */      

      $sin_renovar_subquery=DB::table("asignacion_locker")
      ->join("locker","asignacion_locker.cve_locker","locker.cve_locker")
      ->join("cargo","asignacion_locker.cve_cargo","cargo.cve_cargo")
      ->whereRaw("YEAR(fecha_incio) IN(?,?)",[$periodo-1,$periodo])
      ->whereNotNull("cargo.idpago")
      ->where("locker.propietario",12981)
      ->groupBy("asignacion_locker.cve_locker")
      ->havingRaw("GROUP_CONCAT(YEAR(fecha_incio))=? AND COUNT(asignacion_locker.cve_locker)=1",[$periodo-1])
      ->select("asignacion_locker.cve_locker");

      $sin_renovar=DB::table($sin_renovar_subquery)->count();



         //pertenecen al club   
         // SELECT COUNT(locker.cve_locker) FROM locker          
         // WHERE propietario =12981;

         $pertenence_club=DB::table("locker")->where("propietario",12981)->count();

        

         //no pertenecen al club   
         // SELECT COUNT(locker.cve_locker) FROM locker          
         // WHERE propietario not IN (12981,25660);

         $pertenence_externo=DB::table("locker")->whereNotIn("propietario",[12981,25660])->count();

         

         // SELECT COUNT(asignacion_locker.cve_locker) as cantidad,if(cargo.idpago IS NULL,0,1) as tipo FROM asignacion_locker 
         // INNER JOIN cargo ON asignacion_locker.cve_cargo=cargo.cve_cargo
         // WHERE YEAR(asignacion_locker.fecha_incio)=2024 AND asignacion_locker.estatus=1 GROUP BY if(cargo.idpago IS NULL,0,1);
         /*
               SELECT 
                  CONCAT_WS('/',
                  COUNT(asignacion_locker.cve_locker),
                  COUNT(case when cargo.idpago IS NULL then 1 END )) AS cargos
               FROM asignacion_locker       
               INNER JOIN locker ON asignacion_locker.cve_locker=locker.cve_locker AND locker.propietario=12981    
               inner JOIN cargo ON asignacion_locker.cve_cargo=cargo.cve_cargo  
               WHERE YEAR(asignacion_locker.fecha_incio)=2024
         */

         $cargos_pagos=DB::table("asignacion_locker")
         ->join("locker",function($join){ $join->on("asignacion_locker.cve_locker","locker.cve_locker")->where("locker.propietario",12981);})
         ->join("cargo","asignacion_locker.cve_cargo","cargo.cve_cargo")
         ->whereRaw("YEAR(asignacion_locker.fecha_incio)=?",[$periodo])
         ->selectRaw("CONCAT_WS('/',COUNT(asignacion_locker.cve_locker),COUNT(case when cargo.idpago IS NULL then 1 END )) AS cargos")
         ->value("cargos");

         
         
         /*
          SELECT COUNT(t.cve_locker) FROM(SELECT 
                                                locker.cve_locker
                                          FROM locker 
                                          LEFT JOIN asignacion_locker ON locker.cve_locker=asignacion_locker.cve_locker AND YEAR(asignacion_locker.fecha_incio)=2024
                                          LEFT JOIN cargo ON asignacion_locker.cve_cargo=cargo.cve_cargo AND cargo.idpago IS NOT NULL 
                                          WHERE  locker.propietario=12981 
                                          GROUP BY locker.cve_locker
                                          HAVING sum(cargo.idpago) IS NULL ) AS t
         */
         
         $libres_subquery=DB::table("locker")
         ->leftJoin("asignacion_locker",function($join)use($periodo){
            $join->on("locker.cve_locker","asignacion_locker.cve_locker")
            ->whereRaw("YEAR(asignacion_locker.fecha_incio)=?",[$periodo]);
         })
         ->leftJoin("cargo",function($join){$join->on("asignacion_locker.cve_cargo","cargo.cve_cargo")->whereNotNull("cargo.idpago");})
         ->where("locker.propietario",12981)
         ->groupBy("locker.cve_locker")
         ->havingRaw("SUM(cargo.idpago) IS NULL")
         ->select("locker.cve_locker");

         $libres=DB::table($libres_subquery)->count();
      

         return ["rentados"=>$rentados,"sin_renovar"=>$sin_renovar,"pertenece_club"=>$pertenence_club,"pertenece_externo"=>$pertenence_externo,"cargos_pagos"=>$cargos_pagos,"libres"=>$libres];
   }

   public static function getEstadisticaRentados($periodo)
   {
      /*
            SELECT 
		            locker.numero_locker,
		            edificios.nombre as ubicacion,
		            persona.nombre,persona.apellido_paterno,persona.apellido_materno,
		            CONCAT(acciones.numero_accion,CASE clasificacion when 1 THEN 'A' when 1 THEN 'A' when 1 THEN 'A' ELSE '' END) AS accion,
		            pago.folio,
		            pago.total,
		            asignacion_locker.fecha_incio,
		            asignacion_locker.fecha_fin 
            FROM asignacion_locker 
            INNER JOIN locker ON asignacion_locker.cve_locker=locker.cve_locker
            INNER JOIN edificios ON locker.ubicacion=edificios.cve_edificio
            INNER JOIN persona ON locker.rentador=persona.cve_persona
            INNER JOIN socios ON persona.cve_persona=socios.cve_persona
            LEFT JOIN acciones ON socios.cve_accion=acciones.cve_accion
            INNER JOIN cargo ON asignacion_locker.cve_cargo=cargo.cve_cargo
            INNER JOIN pago ON cargo.idpago=pago.idpago
            WHERE YEAR(asignacion_locker.fecha_incio)=2023 AND cargo.idpago IS NOT NULL AND locker.propietario=12981;
      */

      return DB::table("asignacion_locker")
      ->join("locker" , "asignacion_locker.cve_locker","locker.cve_locker")
      ->join("edificios" , "locker.ubicacion","edificios.cve_edificio")
      ->join("persona" , "locker.rentador","persona.cve_persona")
      ->join("socios" , "persona.cve_persona","socios.cve_persona")
      ->leftJoin("acciones" , "socios.cve_accion","acciones.cve_accion")
      ->join("cargo" , "asignacion_locker.cve_cargo","cargo.cve_cargo")
      ->join("pago" , "cargo.idpago","pago.idpago")
      ->select("locker.numero_locker","edificios.nombre AS ubicacion","persona.nombre","persona.apellido_paterno","persona.apellido_materno","pago.folio","pago.total","asignacion_locker.fecha_incio","asignacion_locker.fecha_fin")
      ->selectRaw("CONCAT(acciones.numero_accion,CASE clasificacion when 1 THEN 'A' when 1 THEN 'A' when 1 THEN 'A' ELSE '' END) AS accion")
      ->whereRaw("YEAR(asignacion_locker.fecha_incio)=?",[$periodo-1])
      ->whereNotNull("cargo.idpago")
      ->where("locker.propietario",12981)
      ->get();
   }

   public static function getEstadisticaNoRenueva($periodo)
   {
      /*
         SELECT 
		         locker.numero_locker,
               edificios.nombre AS ubicacion,
               persona.nombre,
               persona.apellido_paterno,
               persona.apellido_materno,
               IFNULL(CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END),'N/A') AS accion,
               pago.folio,
               pago.total,
               asignacion_locker.fecha_incio,
               asignacion_locker.fecha_fin
	      FROM asignacion_locker 
	      INNER JOIN locker ON asignacion_locker.cve_locker=locker.cve_locker
	      INNER JOIN edificios ON locker.ubicacion=edificios.cve_edificio
	      INNER JOIN persona ON locker.rentador=persona.cve_persona
	      LEFT JOIN socios ON persona.cve_persona=socios.cve_persona
	      LEFT JOIN acciones ON socios.cve_accion=acciones.cve_accion
	      LEFT JOIN cargo ON asignacion_locker.cve_cargo=cargo.cve_cargo
         LEFT JOIN pago ON cargo.idpago=pago.idpago
	      WHERE  YEAR(fecha_incio) IN(2023,2024) AND cargo.idpago IS NOT NULL AND locker.propietario=12981
	      GROUP BY asignacion_locker.cve_locker 
	      HAVING  GROUP_CONCAT(YEAR(fecha_incio))=2023 AND COUNT(asignacion_locker.cve_locker)=1
       */

       return DB::table("asignacion_locker")
       ->join("locker" , "asignacion_locker.cve_locker","locker.cve_locker")
       ->join("edificios" , "locker.ubicacion","edificios.cve_edificio")
       ->join("persona" , "locker.rentador","persona.cve_persona")
       ->leftJoin("socios" , "persona.cve_persona","socios.cve_persona")
       ->leftJoin("acciones" , "socios.cve_accion","acciones.cve_accion")
       ->leftJoin("cargo" , "asignacion_locker.cve_cargo","cargo.cve_cargo")
       ->leftJoin("pago" , "cargo.idpago","pago.idpago")
       ->whereRaw("YEAR(fecha_incio) IN(?,?)",[$periodo-1,$periodo])
       ->whereNotNull("cargo.idpago")
       ->where("locker.propietario",12981)
       ->groupBy("asignacion_locker.cve_locker")
       ->havingRaw("GROUP_CONCAT(YEAR(fecha_incio))=? AND COUNT(asignacion_locker.cve_locker)=1",[$periodo-1])
       ->select("locker.numero_locker",
               "edificios.nombre AS ubicacion",
               "persona.nombre",
               "persona.apellido_paterno",
               "persona.apellido_materno",
               "pago.folio",
               "pago.total",
               "asignacion_locker.fecha_incio",
               "asignacion_locker.fecha_fin")
      ->selectRaw("IFNULL(CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END),'N/A') AS accion")
      ->get();
   }

   public static function getEstadisticaPerteneceClub($periodo)
   {
      /*
               SELECT 
                     locker.numero_locker,
                     edificios.nombre AS ubicacion,
                     persona.nombre,
                     persona.apellido_paterno,
                     persona.apellido_materno,
                     IFNULL(CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END),'N/A') AS accion,
                     pago.folio,
                     pago.total,
                     asignacion_locker.fecha_incio,
                     asignacion_locker.fecha_fin 
               FROM locker 
               LEFT JOIN asignacion_locker ON locker.cve_locker=asignacion_locker.cve_locker AND YEAR(asignacion_locker.fecha_incio)=2024 AND asignacion_locker.estatus=1
               LEFT JOIN edificios ON locker.ubicacion=edificios.cve_edificio
               LEFT JOIN persona ON locker.rentador=persona.cve_persona
               LEFT JOIN socios ON persona.cve_persona=socios.cve_persona
               LEFT JOIN acciones ON socios.cve_accion=acciones.cve_accion
               LEFT JOIN cargo ON asignacion_locker.cve_cargo=cargo.cve_cargo
               LEFT JOIN pago ON cargo.idpago=pago.idpago
               WHERE propietario =12981;
       */

       return DB::table("locker")
       ->leftJoin("asignacion_locker",function($join)use($periodo){$join->on("locker.cve_locker","asignacion_locker.cve_locker")->where("asignacion_locker.estatus",1)->whereRaw("YEAR(asignacion_locker.fecha_incio)=?",[$periodo]);})
       ->leftJoin("edificios" , "locker.ubicacion","edificios.cve_edificio")
       ->leftJoin("persona" , "locker.rentador","persona.cve_persona")
       ->leftJoin("socios" , "persona.cve_persona","socios.cve_persona")
       ->leftJoin("acciones" , "socios.cve_accion","acciones.cve_accion")
       ->leftJoin("cargo" , "asignacion_locker.cve_cargo","cargo.cve_cargo")
       ->leftJoin("pago" , "cargo.idpago","pago.idpago")
       ->where("propietario",12981)
       ->select("locker.numero_locker",
       "edificios.nombre AS ubicacion",
       "persona.nombre",
       "persona.apellido_paterno",
       "persona.apellido_materno",
       "pago.folio",
       "pago.total",
       "asignacion_locker.fecha_incio",
       "asignacion_locker.fecha_fin")
       ->selectRaw("IFNULL(CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END),'N/A') AS accion")
       ->get();
   }

   public static function getEstadisticaPerteneceExterno($periodo)
   {
      /*
               SELECT 
                     locker.numero_locker,
                     edificios.nombre AS ubicacion,
                     persona.nombre,
                     persona.apellido_paterno,
                     persona.apellido_materno,
                     IFNULL(CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END),'N/A') AS accion,
                     pago.folio,
                     pago.total,
                     asignacion_locker.fecha_incio,
                     asignacion_locker.fecha_fin 
               FROM locker 
               left JOIN asignacion_locker ON locker.cve_locker=asignacion_locker.cve_locker AND YEAR(asignacion_locker.fecha_incio)=2024 AND asignacion_locker.estatus=1
               left JOIN edificios ON locker.ubicacion=edificios.cve_edificio
               left JOIN persona ON locker.rentador=persona.cve_persona
               left JOIN socios ON persona.cve_persona=socios.cve_persona
               left JOIN acciones ON socios.cve_accion=acciones.cve_accion
               left JOIN cargo ON asignacion_locker.cve_cargo=cargo.cve_cargo
               LEFT JOIN pago ON cargo.idpago=pago.idpago
               WHERE propietario not IN (12981,25660);
       */

       return DB::table("locker")
       ->leftJoin("asignacion_locker",function($join)use($periodo){$join->on("locker.cve_locker","asignacion_locker.cve_locker")->where("asignacion_locker.estatus",1)->whereRaw("YEAR(asignacion_locker.fecha_incio)=?",[$periodo]);})
       ->leftJoin("edificios" , "locker.ubicacion","edificios.cve_edificio")
       ->leftJoin("persona" , "locker.rentador","persona.cve_persona")
       ->leftJoin("socios" , "persona.cve_persona","socios.cve_persona")
       ->leftJoin("acciones" , "socios.cve_accion","acciones.cve_accion")
       ->leftJoin("cargo" , "asignacion_locker.cve_cargo","cargo.cve_cargo")
       ->leftJoin("pago" , "cargo.idpago","pago.idpago")
       ->whereNotIn("propietario",[12981,25660])
       ->select("locker.numero_locker",
       "edificios.nombre AS ubicacion",
       "persona.nombre",
       "persona.apellido_paterno",
       "persona.apellido_materno",
       "pago.folio",
       "pago.total",
       "asignacion_locker.fecha_incio",
       "asignacion_locker.fecha_fin")
       ->selectRaw("IFNULL(CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END),'N/A') AS accion")
       ->get();
   }

   public static function getEstadisticaCargoOrPagos($periodo)
   {
      /*
         SELECT 
               locker.numero_locker,
               edificios.nombre AS ubicacion,
               persona.nombre,
               persona.apellido_paterno,
               persona.apellido_materno,
               IFNULL(CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END),'N/A') AS accion,
               pago.folio,
               pago.total,
               asignacion_locker.fecha_incio,
               asignacion_locker.fecha_fin,
               IF(pago.idpago IS NULL,0,1) estatus_pago 
         FROM asignacion_locker 
         INNER JOIN locker ON asignacion_locker.cve_locker=locker.cve_locker  AND locker.propietario=12981 
         INNER JOIN edificios ON locker.ubicacion=edificios.cve_edificio
         INNER JOIN persona ON locker.rentador=persona.cve_persona
         LEFT JOIN socios ON persona.cve_persona=socios.cve_persona
         LEFT JOIN acciones ON socios.cve_accion=acciones.cve_accion
         INNER JOIN cargo ON asignacion_locker.cve_cargo=cargo.cve_cargo
         LEFT JOIN pago ON cargo.idpago=pago.idpago
         WHERE YEAR(asignacion_locker.fecha_incio)=2024
      */

      return DB::table("asignacion_locker")
      ->join("locker" , "asignacion_locker.cve_locker","locker.cve_locker")
      ->join("edificios" , "locker.ubicacion","edificios.cve_edificio")
      ->join("persona" , "locker.rentador","persona.cve_persona")
      ->leftJoin("socios" , "persona.cve_persona","socios.cve_persona")
      ->leftJoin("acciones" , "socios.cve_accion","acciones.cve_accion")
      ->join("cargo" ,function($join){$join->on("asignacion_locker.cve_cargo","cargo.cve_cargo")->where("locker.propietario",12981);})
      ->leftJoin("pago" , "cargo.idpago","pago.idpago")
      ->whereRaw("YEAR(asignacion_locker.fecha_incio)=?",[$periodo])
      ->select("locker.numero_locker",
               "edificios.nombre AS ubicacion",
               "persona.nombre",
               "persona.apellido_paterno",
               "persona.apellido_materno",
               "pago.folio",
               "pago.total",
               "asignacion_locker.fecha_incio",
               "asignacion_locker.fecha_fin")
      ->selectRaw("IF(pago.idpago IS NULL,0,1) estatus_pago")
      ->selectRaw("IFNULL(CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END),'N/A') AS accion")
      ->get();
   }

   public static function getEstadisticaLibres($periodo)
   {
      /*
         SELECT 
               locker.numero_locker,
               edificios.nombre AS ubicacion,
               persona.nombre,
               persona.apellido_paterno,
               persona.apellido_materno,
               pago.folio,
               pago.total,
               asignacion_locker_history.fecha_incio,
               asignacion_locker_history.fecha_fin,
               asignacion_locker_history.observaciones,
               asignacion_locker_history.motivo_cancelacion
               asignacion_locker_history.estatus,
               CASE WHEN asignacion_locker_history.cve_asignacion_locker>0 AND pago.idpago IS NOT NULL THEN 1 WHEN asignacion_locker_history.cve_asignacion_locker>0 AND pago.idpago IS NULL THEN 0 ELSE NULL END AS estatus_pago
         FROM locker 
         LEFT JOIN asignacion_locker ON locker.cve_locker=asignacion_locker.cve_locker AND YEAR(asignacion_locker.fecha_incio)=2024 AND asignacion_locker.estatus=1
         LEFT JOIN asignacion_locker AS asignacion_locker_history ON locker.cve_locker=asignacion_locker_history.cve_locker and asignacion_locker_history.fecha_incio=(SELECT MAX(asignacion_locker_xxx.fecha_incio) FROM asignacion_locker AS asignacion_locker_xxx WHERE asignacion_locker_xxx.cve_locker=locker.cve_locker)
         INNER JOIN edificios ON locker.ubicacion=edificios.cve_edificio
         left JOIN persona ON asignacion_locker_history.cve_persona=persona.cve_persona
         left JOIN cargo ON asignacion_locker_history.cve_cargo=cargo.cve_cargo
         LEFT JOIN pago ON cargo.idpago=pago.idpago
         WHERE asignacion_locker.cve_locker IS NULL AND locker.propietario=12981
         GROUP BY locker.cve_locker


         SELECT 
               locker.numero_locker,
               edificios.nombre AS ubicacion,
               persona.nombre,
               persona.apellido_paterno,
               persona.apellido_materno,
               pago.folio,
               pago.total,
               asignacion_locker_history.fecha_incio,
               asignacion_locker_history.fecha_fin,
               asignacion_locker_history.observaciones,
               asignacion_locker_history.motivo_cancelacion,
               asignacion_locker_history.estatus,
               CASE WHEN asignacion_locker_history.cve_asignacion_locker>0 AND pago.idpago IS NOT NULL THEN 1 WHEN asignacion_locker_history.cve_asignacion_locker>0 AND pago.idpago IS NULL THEN 0 ELSE NULL END AS estatus_pago
         FROM locker 
         LEFT JOIN asignacion_locker ON locker.cve_locker=asignacion_locker.cve_locker AND YEAR(asignacion_locker.fecha_incio)=2024
         LEFT JOIN cargo ON asignacion_locker.cve_cargo=cargo.cve_cargo AND cargo.idpago IS NOT NULL
         INNER JOIN edificios ON locker.ubicacion=edificios.cve_edificio         
         
         LEFT JOIN asignacion_locker AS asignacion_locker_history ON locker.cve_locker=asignacion_locker_history.cve_locker AND asignacion_locker_history.fecha_incio =(SELECT MAX(fecha_incio) FROM asignacion_locker WHERE cve_locker=locker.cve_locker)
         LEFT JOIN cargo AS cargo_history ON asignacion_locker_history.cve_cargo=cargo_history.cve_cargo 
         LEFT JOIN persona ON asignacion_locker_history.cve_persona=persona.cve_persona
         LEFT JOIN pago ON cargo_history.idpago=pago.idpago
         
         WHERE  locker.propietario=12981
         GROUP BY locker.cve_locker
         HAVING SUM(cargo.idpago) IS NULL
      */

      return DB::table("locker")
      ->leftJoin("asignacion_locker",function($join)use($periodo){$join->on("locker.cve_locker","asignacion_locker.cve_locker")->whereRaw("YEAR(asignacion_locker.fecha_incio)=?",[$periodo]);})
      ->leftJoin("cargo" ,function($join){ $join->on("asignacion_locker.cve_cargo","cargo.cve_cargo")->whereNotNull("cargo.idpago");})
      ->join("edificios" , "locker.ubicacion","edificios.cve_edificio")
      
      ->leftJoin("asignacion_locker AS asignacion_locker_history",function($join){
         $join->on("locker.cve_locker","asignacion_locker_history.cve_locker")
         ->where("asignacion_locker_history.fecha_incio","=",function($join){
            $join->select(DB::raw("MAX(fecha_incio)"))->from("asignacion_locker")->whereColumn("cve_locker","locker.cve_locker");
         });
      })         
      ->leftJoin("persona" , "asignacion_locker_history.cve_persona","persona.cve_persona")
      ->leftJoin("cargo AS cargo_history" , "asignacion_locker_history.cve_cargo","cargo_history.cve_cargo")
      ->leftJoin("pago" , "cargo_history.idpago","pago.idpago")
      ->where("locker.propietario",12981)
      ->groupBy("locker.cve_locker")
      ->havingRaw("SUM(cargo.idpago) IS NULL")
      ->select("locker.numero_locker",
               "edificios.nombre AS ubicacion",
               "persona.nombre",
               "persona.apellido_paterno",
               "persona.apellido_materno",
               "pago.folio",
               "pago.total",
               "asignacion_locker_history.fecha_incio",
               "asignacion_locker_history.fecha_fin",
               "asignacion_locker_history.observaciones",
               "asignacion_locker_history.motivo_cancelacion",
               "asignacion_locker_history.estatus")
      ->selectRaw("CASE WHEN asignacion_locker_history.cve_asignacion_locker>0 AND pago.idpago IS NOT NULL THEN 1 WHEN asignacion_locker_history.cve_asignacion_locker>0 AND pago.idpago IS NULL THEN 0 ELSE NULL END AS estatus_pago")
      ->get();
      

   }
   
}
