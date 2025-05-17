<?php
namespace App\DAO;
use App\Entity\Accionista;
use App\Entity\Persona;
use App\Entity\Colonia;
use App\Entity\Direccion;
use App\Entity\Accion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ReporteGeneralDAO {

    public function __construct(){}
    /**
     * 
     */
      
    public static function getReporteGeneral($fecha_inicio,$fecha_fin,$periodo)
    {

        /* 
            //historico acciones muestra acciones cambio de estatus ,cambio de dueño y cambio de tipo accion 
            SELECT 

                CASE 
	            WHEN (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno!=acciones.cve_dueno) then 'Cambio de dueño'
	            WHEN (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion!=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno) then 'Cambio de tipo accion' 
                WHEN (estatus_anterior=1 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno) then 'Cambio estatus accion activo a bloqueado'
                WHEN (estatus_anterior=1 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno) then 'Cambio estatus accion activo a inactivo'
                WHEN (estatus_anterior=2 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno) then 'Cambio estatus accion bloqueado a activo'
                WHEN (estatus_anterior=2 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno) then 'Cambio estatus accion bloqueado a inactivo'
                WHEN (estatus_anterior=3 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno) then 'Cambio estatus accion inactivo a activo'
                WHEN (estatus_anterior=3 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno) then 'Cambio estatus accion inactivo a bloqueado'
                ELSE 'No aplica' END AS movimiento,
    
                COUNT(cve_acciones_historico) AS total,
    
                SUM(CASE 
	            WHEN (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno!=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=2) then 1
	            WHEN (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion!=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=2) then 1 
                WHEN (estatus_anterior=1 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=2) then 1
                WHEN (estatus_anterior=1 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=2) then 1
                WHEN (estatus_anterior=2 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=2) then 1
                WHEN (estatus_anterior=2 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=2) then 1
                WHEN (estatus_anterior=3 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=2) then 1
                WHEN (estatus_anterior=3 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=2) then 1
                ELSE 0 END) AS total_multiple,
    
                SUM(CASE 
	            WHEN (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno!=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=3) then 1
	            WHEN (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion!=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=3) then 1 
                WHEN (estatus_anterior=1 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=3) then 1
                WHEN (estatus_anterior=1 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=3) then 1
                WHEN (estatus_anterior=2 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=3) then 1
                WHEN (estatus_anterior=2 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=3) then 1
                WHEN (estatus_anterior=3 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=3) then 1
                WHEN (estatus_anterior=3 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=3) then 1
                ELSE 0 END) AS total_familiar,
    
                SUM(CASE 
	            WHEN (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno!=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=8) then 1
	            WHEN (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion!=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=8) then 1 
                WHEN (estatus_anterior=1 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=8) then 1
                WHEN (estatus_anterior=1 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=8) then 1
                WHEN (estatus_anterior=2 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=8) then 1
                WHEN (estatus_anterior=2 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=8) then 1
                WHEN (estatus_anterior=3 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=8) then 1
                WHEN (estatus_anterior=3 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno AND acciones_historico.cve_tipo_accion=8) then 1
                ELSE 0 END) AS total_consejo
    
            FROM acciones_historico 
            INNER JOIN acciones ON acciones_historico.cve_accion=acciones.cve_accion
            WHERE  fecha_modificacion BETWEEN '2024-01-01 00:00:00' AND '2024-01-31 23:59:59' AND acciones.numero_accion<=1500
            GROUP BY 
            #es cambio de dueño
            (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno!=acciones.cve_dueno),
            #es cambio de tipo accion 
            (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion!=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno),
            #cambio accion de activa a bloqueada 
            (estatus_anterior=1 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno),
            #cambio accion de activa a baja
            (estatus_anterior=1 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno),
            #cambio de accion de bloqueada a activa
            (estatus_anterior=2 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno),
            #cambio de accion de bloqueada a baja
            (estatus_anterior=2 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno),
            #cambio de accion baja a activa
            (estatus_anterior=3 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno),
            #cambio de accion baja a bloqueada
            (estatus_anterior=3 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno)

            ORDER BY (estatus_anterior=estatus_actual) DESC, estatus_anterior,estatus_actual

            //busca los estatus actuales de lacciones activas bloqueadas y de baja y los socios activos
                SELECT acciones.estatus, COUNT(acciones.estatus) AS numero
                FROM acciones
                WHERE acciones.numero_accion <= 1500
                GROUP BY acciones.estatus

                UNION 

                SELECT 0,COUNT(*) FROM socios
                INNER JOIN acciones ON socios.cve_accion=acciones.cve_accion WHERE acciones.numero_accion<=1500


            //cargos cobrados y por cobrar 

            ESTA YA NO SIRVE -----SELECT 
                cve_cuota, 
                concepto, 
                SUM(total) AS total, 
                COUNT(cargo.cve_cargo) AS cantidad_cuotas, 
                IFNULL(SUM(descuento.monto),0) AS descuento, 
                SUM(total)- IFNULL(SUM(descuento.monto),0) AS total_final, 
                if(idpago IS NULL,0,1) AS cobrado
            FROM cargo
            LEFT JOIN descuento ON cargo.cve_cargo = descuento.cve_cargo
            WHERE cve_cuota IN (1, 2, 3, 4, 5,1009,1010,1011,1012) AND fecha_cargo BETWEEN '2024-01-01' AND '2024-01-31'
            GROUP BY cve_cuota IN (1,1009),cve_cuota IN (2,1010),cve_cuota IN (3,1011),cve_cuota IN (4,1012),cve_cuota IN(5) , idpago IS NULL;----NOSIRVE 

            SELECT 
	            cve_cuota, 
	            concepto,
                (SUM(IF(cargo.idpago IS NULL,1,0))+SUM(IF(cargo.idpago IS NOT NULL,1,0))) total_cargos,
                SUM(IF(cargo.idpago IS NOT NULL,1,0)) total_cargos_no_pagados,
	            SUM(IF(cargo.idpago IS NULL,1,0)) AS total_cargos_no_pagados,
   
                SUM(CASE cve_cuota WHEN 1 THEN 1 WHEN 2 THEN 1 WHEN 3 THEN 1 WHEN 4 THEN 1 WHEN 5 THEN 1 ELSE 0 END)AS total_cargos_periodo_actual ,
   
                SUM(CASE WHEN cve_cuota=1 AND cargo.idpago IS NOT NULL  THEN 1 WHEN cve_cuota=2 AND cargo.idpago IS NOT NULL THEN 1 WHEN cve_cuota=3 AND cargo.idpago IS NOT NULL THEN 1 WHEN cve_cuota=4 AND cargo.idpago IS NOT NULL THEN 1 WHEN cve_cuota=5 AND cargo.idpago IS NOT NULL THEN 1 ELSE 0 END)AS total_cargos_periodo_actual_pagados ,

                SUM(CASE WHEN cve_cuota=1 AND cargo.idpago IS NULL  THEN 1 WHEN cve_cuota=2 AND cargo.idpago IS NULL THEN 1 WHEN cve_cuota=3 AND cargo.idpago IS NULL THEN 1 WHEN cve_cuota=4 AND cargo.idpago IS NULL THEN 1 WHEN cve_cuota=5 AND cargo.idpago IS NULL THEN 1 ELSE 0 END) AS total_cargos_periodo_actual_no_pagados ,

                SUM(CASE WHEN cve_cuota=1009 AND cargo.idpago IS NOT NULL THEN 1 WHEN cve_cuota=1010 AND cargo.idpago IS NOT NULL THEN 1 WHEN cve_cuota=1011 AND cargo.idpago IS NOT NULL THEN 1 WHEN cve_cuota=1012 AND cargo.idpago IS NOT NULL THEN 1 WHEN cve_cuota=1013 AND cargo.idpago IS NOT NULL THEN 1 ELSE 0 END) AS total_pagos_anticipados
      	
            FROM cargo
            LEFT JOIN descuento ON cargo.cve_cargo = descuento.cve_cargo
            WHERE cve_cuota IN (1, 2, 3, 4, 5,1009,1010,1011,1012) AND fecha_cargo BETWEEN '2024-01-01 00:00:00' AND '2024-01-31 23:59:59'
            GROUP BY cve_cuota IN(1009,1),cve_cuota IN(2,1010),cve_cuota IN(3,1011),cve_cuota IN (4,1012),cve_cuota IN(5)
      
 
            //busca los movimeintos de los socios en las acciones como altas de socios ,cambios de aqccion ,bloqueo s y bajas de socios
            SELECT 
                movimiento,COUNT(movimiento) AS total
            FROM socio_accion 
            WHERE socio_accion.fecha_hora_movimiento BETWEEN '2023-01-01' AND '2023-10-01'
            GROUP BY movimiento;

            cuotas canceladas desde el demonio cron 

            SELECT 
	            cancelar_cargo.cve_cuota,cancelar_cargo.concepto,COUNT(cancelar_cargo.cve_cancelar_cargo) AS cantidad
            FROM cancelar_cargo WHERE motivo_cancelacion REGEXP '^cron\\|' AND fecha_cancelacion BETWEEN '2023-01-01' AND '2023-12-01'
            GROUP BY cancelar_cargo.cve_cuota;
        
        */

        try {
          
            $yyyy=Str::substr($periodo,0,4);
            $mm=Str::substr($periodo,4,6);
            // (SELECT 1 AS estatus,activas AS numero FROM resumen_acciones WHERE periodo='02-2024' LIMIT 1) UNION 
            // (SELECT 2,bloqueadas FROM resumen_acciones WHERE periodo='02-2024' LIMIT 1) UNION 
            // (SELECT 3,inactivas FROM resumen_acciones WHERE periodo='02-2024' LIMIT 1) UNION 
            // (SELECT 0, socios_activos FROM resumen_acciones WHERE periodo='02-2024' LIMIT 1)
            $estatus_actuales_acciones=[];

            $data=DB::query()->fromSub(function($sub) use($mm,$yyyy){
               $sub->from("resumen_acciones")->where("periodo","$mm-$yyyy")->select(DB::raw("1 AS estatus"),"activas AS numero")->limit(1)
               ->union(DB::table("resumen_acciones")->where("periodo","$mm-$yyyy")->select(DB::raw("2 AS estatus"),"bloqueadas AS numero")->limit(1))
               ->union(DB::table("resumen_acciones")->where("periodo","$mm-$yyyy")->select(DB::raw("3 AS estatus"),"inactivas AS numero")->limit(1))
               ->union(DB::table("resumen_acciones")->where("periodo","$mm-$yyyy")->select(DB::raw("0 AS estatus"),"socios_activos AS numero")->limit(1));
            },"t");            
            if($data->exists())
            {
                $estatus_actuales_acciones=$data->get();
            }
            else{
                $estatus_actuales_acciones=DB::table("acciones")
                ->select("acciones.estatus")
                ->selectRaw("COUNT(acciones.estatus) AS numero")
                ->where("acciones.numero_accion","<=",1500)
                ->where("acciones.numero_accion","!=",0)
                ->groupBy("acciones.estatus")
                ->union(DB::table("socios")->join("acciones","socios.cve_accion","acciones.cve_accion")->where("acciones.numero_accion","<=",1500)->select(DB::raw("0"),DB::raw("COUNT(socios.cve_socio)")))
                ->get();                           
            }               

            // dd($estatus_actuales_acciones);
                           
            $cambios_acciones_t1=DB::table("acciones_historico")
            // ->join("acciones" , "acciones_historico.cve_accion","acciones.cve_accion")
            ->whereRaw("acciones_historico.fecha_modificacion BETWEEN ? AND ?",[$fecha_inicio,$fecha_fin])
            ->where("acciones_historico.numero_accion","<=",1500)
            ->where("acciones_historico.numero_accion","!=",0)
            ->groupByRaw("(estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno!=acciones_historico.cve_dueno_actual)")
            ->groupByRaw("(estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion!=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual)")
            ->groupByRaw("(estatus_anterior=1 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual)")
            ->groupByRaw("(estatus_anterior=1 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual)")
            ->groupByRaw("(estatus_anterior=2 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual)")
            ->groupByRaw("(estatus_anterior=2 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual)")
            ->groupByRaw("(estatus_anterior=3 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual)")
            ->groupByRaw("(estatus_anterior=3 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual)")
            ->orderByRaw("(estatus_anterior=estatus_actual) DESC")
            ->orderBy("estatus_anterior")
            ->orderBy("estatus_actual")
            ->selectRaw("CASE WHEN (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno!=acciones_historico.cve_dueno_actual) THEN 1 
            WHEN (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion!=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual) THEN 2 
            WHEN (estatus_anterior=1 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual) THEN 3 
            WHEN (estatus_anterior=1 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual) THEN 4 
            WHEN (estatus_anterior=2 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual) THEN 5 
            WHEN (estatus_anterior=2 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual) THEN 6 
            WHEN (estatus_anterior=3 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual) THEN 7 
            WHEN (estatus_anterior=3 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual) THEN 8 
            ELSE 0 END AS id")
            ->selectRaw("COUNT(cve_acciones_historico) AS total")
            ->selectRaw("SUM(
                CASE WHEN (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno!=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=2) THEN 1 
                      WHEN (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion!=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=2) THEN 1 
                      WHEN (estatus_anterior=1 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=2) THEN 1 
                      WHEN (estatus_anterior=1 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=2) THEN 1 
                      WHEN (estatus_anterior=2 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=2) THEN 1 
                      WHEN (estatus_anterior=2 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=2) THEN 1 
                      WHEN (estatus_anterior=3 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=2) THEN 1 
                      WHEN (estatus_anterior=3 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=2) THEN 1 
                      ELSE 0 END) AS total_multiple")
            ->selectRaw("SUM(
                CASE WHEN (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno!=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=3) THEN 1 
                      WHEN (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion!=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=3) THEN 1 
                      WHEN (estatus_anterior=1 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=3) THEN 1 
                      WHEN (estatus_anterior=1 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=3) THEN 1 
                      WHEN (estatus_anterior=2 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=3) THEN 1 
                      WHEN (estatus_anterior=2 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=3) THEN 1 
                      WHEN (estatus_anterior=3 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=3) THEN 1 
                      WHEN (estatus_anterior=3 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=3) THEN 1 
                      ELSE 0 END) AS total_familiar")
            ->selectRaw("SUM(
                CASE WHEN (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno!=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=8) THEN 1 
                      WHEN (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion!=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=8) THEN 1 
                      WHEN (estatus_anterior=1 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=8) THEN 1 
                      WHEN (estatus_anterior=1 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=8) THEN 1 
                      WHEN (estatus_anterior=2 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=8) THEN 1 
                      WHEN (estatus_anterior=2 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=8) THEN 1 
                      WHEN (estatus_anterior=3 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=8) THEN 1 
                      WHEN (estatus_anterior=3 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones_historico.cve_tipo_accion_actual AND acciones_historico.cve_dueno=acciones_historico.cve_dueno_actual AND acciones_historico.cve_tipo_accion=8) THEN 1 
                      ELSE 0 END) AS total_consejo");

            // $cambios_acciones=DB::table( DB::raw("(SELECT * FROM (
            //     SELECT 1 AS id,'Cambio dueño' AS movimiento UNION 
            //     SELECT 2,'Cambio tipo accion'  UNION 
            //     SELECT 3,'Cambio activo a bloqueado'  UNION 
            //     SELECT 4,'Cambio activo a inactivo'  UNION 
            //     SELECT 5,'Cambio bloqueado a activo'  UNION 
            //     SELECT 6,'Cambio bloqueado a inactivo'  UNION 
            //     SELECT 7,'Cambio inactivo a activo'  UNION 
            //     SELECT 8,'Cambio inactivo a bloqueado'  
            //     ) AS t1) as t1") )
            //     ->leftJoinSub($cambios_acciones_t1,"t2",function($join){$join->on("t1.id","t2.id");})
            //     ->select("t1.id","t1.movimiento")
            //     ->selectRaw("IFNULL(t2.total,0) AS total")
            //     ->selectRaw("IFNULL(t2.total_multiple,0) AS total_multiple")
            //     ->selectRaw("IFNULL(t2.total_familiar,0) AS total_familiar")
            //     ->selectRaw("IFNULL(t2.total_consejo,0) AS total_consejo")
            //     ->get();

            $cambios_acciones=DB::query()->fromSub("SELECT 1 AS id,'Cambio dueño' AS movimiento UNION 
            SELECT 2,'Cambio tipo accion'  UNION 
            SELECT 3,'Cambio activo a bloqueado'  UNION 
            SELECT 4,'Cambio activo a inactivo'  UNION 
            SELECT 5,'Cambio bloqueado a activo'  UNION 
            SELECT 6,'Cambio bloqueado a inactivo'  UNION 
            SELECT 7,'Cambio inactivo a activo'  UNION 
            SELECT 8,'Cambio inactivo a bloqueado'","t1")
            ->leftJoinSub($cambios_acciones_t1,"t2",function($join){$join->on("t1.id","t2.id");})
            ->select("t1.id","t1.movimiento")
            ->selectRaw("IFNULL(t2.total,0) AS total")
            ->selectRaw("IFNULL(t2.total_multiple,0) AS total_multiple")
            ->selectRaw("IFNULL(t2.total_familiar,0) AS total_familiar")
            ->selectRaw("IFNULL(t2.total_consejo,0) AS total_consejo")
            ->get();            


            $cargos_cobrados_faltantes=DB::table("cargo")
            ->join("acciones","cargo.cve_accion","acciones.cve_accion")
            ->leftJoin("descuento" , "cargo.cve_cargo","descuento.cve_cargo")
            ->where("acciones.numero_accion","<=",1500)
            ->whereIn("cve_cuota",[1,2,3,4,5,1009,1010,1011,1012])
            ->whereRaw("fecha_cargo BETWEEN ? AND ?",[$fecha_inicio,$fecha_fin])
            ->groupByRaw("cve_cuota IN (1,1009)")
            ->groupByRaw("cve_cuota IN (2,1010)")
            ->groupByRaw("cve_cuota IN (3,1011)")
            ->groupByRaw("cve_cuota IN (4,1012)")
            ->groupByRaw("cve_cuota IN (5)")
            ->select("cve_cuota","concepto")
            ->selectRaw("(SUM(IF(cargo.idpago IS NULL,1,0))+SUM(IF(cargo.idpago IS NOT NULL,1,0))) total_cargos")
            ->selectRaw("SUM(IF(cargo.idpago IS NOT NULL,1,0)) total_cargos_pagados")
            ->selectRaw("SUM(IF(cargo.idpago IS NULL,1,0)) AS total_cargos_no_pagados")
            ->selectRaw("SUM(CASE cve_cuota WHEN 1 THEN 1 WHEN 2 THEN 1 WHEN 3 THEN 1 WHEN 4 THEN 1 WHEN 5 THEN 1 ELSE 0 END) AS total_cargos_periodo_actual")
            ->selectRaw("SUM(CASE WHEN cve_cuota=1 AND cargo.idpago IS NOT NULL  THEN 1 WHEN cve_cuota=2 AND cargo.idpago IS NOT NULL THEN 1 WHEN cve_cuota=3 AND cargo.idpago IS NOT NULL THEN 1 WHEN cve_cuota=4 AND cargo.idpago IS NOT NULL THEN 1 WHEN cve_cuota=5 AND cargo.idpago IS NOT NULL THEN 1 ELSE 0 END) AS total_cargos_periodo_actual_pagados")
            ->selectRaw("SUM(CASE WHEN cve_cuota=1 AND cargo.idpago IS NULL  THEN 1 WHEN cve_cuota=2 AND cargo.idpago IS NULL THEN 1 WHEN cve_cuota=3 AND cargo.idpago IS NULL THEN 1 WHEN cve_cuota=4 AND cargo.idpago IS NULL THEN 1 WHEN cve_cuota=5 AND cargo.idpago IS NULL THEN 1 ELSE 0 END) AS total_cargos_periodo_actual_no_pagados")
            ->selectRaw("SUM(CASE WHEN cve_cuota=1009  THEN 1 WHEN cve_cuota=1010  THEN 1 WHEN cve_cuota=1011 THEN 1 WHEN cve_cuota=1012 THEN 1 WHEN cve_cuota=1013 THEN 1 ELSE 0 END) AS total_pagos_anticipados")
            ->selectRaw("SUM(CASE WHEN cve_cuota=1009 AND cargo.idpago IS NOT NULL THEN 1 WHEN cve_cuota=1010 AND cargo.idpago IS NOT NULL THEN 1 WHEN cve_cuota=1011 AND cargo.idpago IS NOT NULL THEN 1 WHEN cve_cuota=1012 AND cargo.idpago IS NOT NULL THEN 1 WHEN cve_cuota=1013 AND cargo.idpago IS NOT NULL THEN 1 ELSE 0 END) AS total_pagos_anticipados_pagados")
            ->selectRaw("SUM(CASE WHEN cve_cuota=1009 AND cargo.idpago IS NULL THEN 1 WHEN cve_cuota=1010 AND cargo.idpago IS NULL THEN 1 WHEN cve_cuota=1011 AND cargo.idpago IS NULL THEN 1 WHEN cve_cuota=1012 AND cargo.idpago IS NULL THEN 1 WHEN cve_cuota=1013 AND cargo.idpago IS NULL THEN 1 ELSE 0 END) AS total_pagos_anticipados_no_pagados")
            ->selectRaw("GROUP_CONCAT(DISTINCT cve_cuota) AS cuotas_in")
            ->selectRaw("MIN(cve_cuota) AS cuota_actual")
            ->selectRaw("MAX(cve_cuota) AS cuota_adelantado")
            ->get();


            //son los cargos anteriores pagados en el perido actual
            $cargos_periodo_anteriores=DB::table("pago")
            ->join("cargo","pago.idpago","cargo.idpago")
            ->join("acciones","cargo.cve_accion","acciones.cve_accion")
            ->where("acciones.numero_accion","<=",1500)
            ->whereRaw("pago.fecha_hora_cobro  BETWEEN ? AND ?",[$fecha_inicio,$fecha_fin])
            ->whereIn("cargo.cve_cuota",[1,2,3,4,5])
            ->whereRaw("PERIOD_DIFF(?,CONCAT(RIGHT(cargo.periodo, 4) ,LEFT(cargo.periodo, 2))) >0",[$periodo])
            ->groupBy("cargo.cve_cuota")
            ->select("cargo.cve_cuota", DB::raw("COUNT(pago.idpago) AS total"))
            ->get();     
            
            
            $cargos_periodo_anteriores_no_pagados=DB::table("cargo")->join("acciones","cargo.cve_accion","acciones.cve_accion")->where("acciones.numero_accion","<=",1500)->whereIn("cargo.cve_cuota",[1,2,3,4,5])->whereRaw("PERIOD_DIFF(?,CONCAT(RIGHT(cargo.periodo, 4) ,LEFT(cargo.periodo, 2))) >0",[$periodo])
            ->whereNull("cargo.idpago")->where("cargo.cve_accion",">",0)->groupBy("cargo.cve_cuota")->select("cargo.cve_cuota", DB::raw("COUNT(cargo.cve_cuota) AS total"))->get();

            $cargos_periodo_anteriores_2mant=    DB::table("cargo")->join("acciones","cargo.cve_accion","acciones.cve_accion")->where("acciones.numero_accion","<=",1500)->whereIn("cargo.cve_cuota",[1,2,3,4,5])->whereRaw("PERIOD_DIFF(?,CONCAT(RIGHT(cargo.periodo, 4) ,LEFT(cargo.periodo, 2))) >0",[$periodo])
            ->whereNull("cargo.idpago")->where("cargo.cve_accion",">",0)->groupBy("cargo.cve_cuota")->groupBy("cargo.cve_accion")->havingRaw("COUNT(cargo.cve_accion)=?",[2])->select("cargo.cve_cuota");
            $cargos_periodo_anteriores_2mant_tbl=DB::table($cargos_periodo_anteriores_2mant,"tbl")->groupBy("tbl.cve_cuota")->select("tbl.cve_cuota",DB::raw("COUNT(tbl.cve_cuota) AS total"))->get();

            $cargos_periodo_anteriores_3mant=    DB::table("cargo")->join("acciones","cargo.cve_accion","acciones.cve_accion")->where("acciones.numero_accion","<=",1500)->whereIn("cargo.cve_cuota",[1,2,3,4,5])->whereRaw("PERIOD_DIFF(?,CONCAT(RIGHT(cargo.periodo, 4) ,LEFT(cargo.periodo, 2))) >0",[$periodo])
            ->whereNull("cargo.idpago")->where("cargo.cve_accion",">",0)->groupBy("cargo.cve_cuota")->groupBy("cargo.cve_accion")->havingRaw("COUNT(cargo.cve_accion)=?",[3])->select("cargo.cve_cuota");
            $cargos_periodo_anteriores_3mant_tbl=DB::table($cargos_periodo_anteriores_3mant,"tbl")->groupBy("tbl.cve_cuota")->select("tbl.cve_cuota",DB::raw("COUNT(tbl.cve_cuota) AS total"))->get();


            //movimientos socios
            $cambios_socios=DB::table("socio_accion")
            ->whereRaw("socio_accion.fecha_hora_movimiento BETWEEN ? AND ?",[$fecha_inicio,$fecha_fin])
            ->groupBy("movimiento")
            ->select( "movimiento")
            ->selectRaw("CAST(movimiento AS UNSIGNED) AS id")
            ->selectRaw("COUNT(movimiento) AS total")
            ->get();

            //cuotas canceladas desde el demonio cron 
            $cargos_cancelados_cron=DB::table("cancelar_cargo")
            ->whereRaw("motivo_cancelacion REGEXP '^cron\\\|'")
            ->whereRaw("fecha_cancelacion BETWEEN ? AND ?",[$fecha_inicio,$fecha_fin])
            ->select("cancelar_cargo.cve_cuota","cancelar_cargo.concepto","motivo_cancelacion")
            ->selectRaw("COUNT(cancelar_cargo.cve_cancelar_cargo) AS cantidad")
            ->groupBy("cancelar_cargo.cve_cuota")
            ->get();

            return [
                "acciones_mov"=>$cambios_acciones,
                "acciones_est"=>$estatus_actuales_acciones,
                "cargos"=>$cargos_cobrados_faltantes,
                "cargos_ateriores"=>["pagado"=>$cargos_periodo_anteriores,"debe"=>$cargos_periodo_anteriores_no_pagados,"mant2"=>$cargos_periodo_anteriores_2mant_tbl,"mant3"=>$cargos_periodo_anteriores_3mant_tbl],
                "socios_mov"=>$cambios_socios,
                "cargos_cancelados_cron"=>$cargos_cancelados_cron];

        } catch (\Exception $e) { 
            return $e;                       
            return [];
        }
    }

    public static function getReporteGeneralDetalle($fecha_inicio,$fecha_fin,$cuotas,$pagado=null)
    {
        try{
            /*
                SELECT 
	                cargo.cve_cuota, 
	                cargo.concepto,
	                cargo.periodo,
	                cargo.fecha_cargo,
	                cargo.total,
	                IFNULL(descuento.monto,0) AS total_descuento,
	                CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_name,
	                persona.nombre,
	                persona.apellido_paterno,
	                persona.apellido_materno	
                FROM cargo
                INNER JOIN acciones ON cargo.cve_accion=acciones.cve_accion
                INNER JOIN persona ON cargo.cve_persona=persona.cve_persona
                LEFT JOIN descuento ON cargo.cve_cargo = descuento.cve_cargo
                WHERE fecha_cargo BETWEEN '2024-01-01 00:00:00' AND '2024-01-31 23:59:59'
                and cve_cuota IN(1009) AND cargo.idpago IS null
            */

                $query=DB::table("cargo")
                ->join("acciones" , "cargo.cve_accion","acciones.cve_accion")
                ->join("persona" , "cargo.cve_persona","persona.cve_persona")
                ->leftJoin("socios","cargo.cve_persona","socios.cve_persona")
                ->leftJoin("descuento" , "cargo.cve_cargo" , "descuento.cve_cargo")
                ->whereRaw("cargo.fecha_cargo BETWEEN ? AND ?",[$fecha_inicio,$fecha_fin])
                ->where("acciones.numero_accion","<=",1500)
                ->whereIn("cargo.cve_cuota",$cuotas)
                ->select(
                "cargo.cve_cuota", 
                "cargo.concepto",
                "cargo.periodo",
                "cargo.fecha_cargo",
                "cargo.total",
                "persona.nombre",
                "persona.apellido_paterno",
                "persona.apellido_materno")
                ->selectRaw("IFNULL(descuento.monto,0) AS total_descuento")
                ->selectRaw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_name")
                ->selectRaw("IFNULL(socios.posicion,'-') AS posicion");

                if(($pagado??false)=="si")
                {
                    $query->whereNotNull("cargo.idpago");
                }
                if(($pagado??false)=="no")
                {
                    $query->whereNull("cargo.idpago");
                }                
                 
                return $query->get();

        }
        catch(\Exception $e)
        {

        }
    }

    public static function getReporteGeneralCargosAnterioresDetalle($fecha_inicio,$fecha_fin,$cuota,$periodo)
    {
        try{
           
            /*
                SELECT 
	                cargo.cve_cuota, 
	                cargo.concepto,
	                cargo.periodo,
	                cargo.fecha_cargo,
	                cargo.total,
	                IFNULL(descuento.monto,0) AS total_descuento,
	                CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_name,
	                IFNULL(socios.posicion,'-') AS posicion,
	                persona.nombre,
	                persona.apellido_paterno,
	                persona.apellido_materno
                FROM pago
                INNER JOIN cargo ON pago.idpago=cargo.idpago
                INNER JOIN acciones ON cargo.cve_accion=acciones.cve_accion
                INNER JOIN persona ON cargo.cve_persona=persona.cve_persona
                LEFT JOIN socios ON cargo.cve_persona=socios.cve_persona
                LEFT JOIN descuento ON cargo.cve_cargo = descuento.cve_cargo
                WHERE pago.fecha_hora_cobro BETWEEN '2024-01-01 00:00:00' AND '2024-01-31 23:59:59' AND cargo.cve_cuota =1 AND PERIOD_DIFF(202401, CONCAT(
                RIGHT(cargo.periodo, 4),
                LEFT(cargo.periodo, 2))) >0;
            */

                $query=DB::table("pago")
                ->join("cargo" , "pago.idpago","cargo.idpago")
                ->join("acciones" , "cargo.cve_accion","acciones.cve_accion")
                ->join("persona" , "cargo.cve_persona","persona.cve_persona")
                ->leftJoin("socios" , "cargo.cve_persona","socios.cve_persona")
                ->leftJoin("descuento" , "cargo.cve_cargo" , "descuento.cve_cargo")
                ->whereRaw("pago.fecha_hora_cobro BETWEEN ? AND ?",[$fecha_inicio,$fecha_fin])
                ->whereRaw("PERIOD_DIFF(?,CONCAT(RIGHT(cargo.periodo, 4) ,LEFT(cargo.periodo, 2))) >0",[$periodo])
                ->select(
                "cargo.cve_cuota", 
                "cargo.concepto",
                "cargo.periodo",
                "cargo.fecha_cargo",
                "cargo.total",
                "persona.nombre",
                "persona.apellido_paterno",
                "persona.apellido_materno")
                ->selectRaw("IFNULL(descuento.monto,0) AS total_descuento")
                ->selectRaw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_name")
                ->selectRaw("IFNULL(socios.posicion,'-') AS posicion");

                if($cuota??false)
                {
                    $query->where("cargo.cve_cuota",$cuota);
                }
                              
                 
                return $query->get();

        }
        catch(\Exception $e)
        {
            return $e;
        }
    }

    public static function getReporteGeneralCargosAnterioresFaltantesDetalle($cuota,$periodo)
    {
        try{
            
            /*
                SELECT 

                    cargo.cve_cuota, 
	                cargo.concepto,
	                cargo.periodo,
	                cargo.fecha_cargo,
	                cargo.total,
	                IFNULL(descuento.monto,0) AS total_descuento,
	                CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_name,
	                IFNULL(socios.posicion,'-') AS posicion,
	                persona.cve_persona,
	                persona.nombre,
	                persona.apellido_paterno,
	                persona.apellido_materno
 
                FROM cargo
                INNER JOIN acciones ON cargo.cve_accion=acciones.cve_accion
                INNER JOIN persona ON cargo.cve_persona=persona.cve_persona
                LEFT JOIN socios ON cargo.cve_persona=socios.cve_persona
                LEFT JOIN descuento ON cargo.cve_cargo = descuento.cve_cargo
                WHWERE cargo.cve_cuota =1 AND PERIOD_DIFF(202401,CONCAT(RIGHT(cargo.periodo, 4) ,LEFT(cargo.periodo, 2))) >0 
                AND cargo.idpago IS NULL AND cargo.cve_accion>0;
            */

                $query=DB::table("cargo")
                ->join("acciones" , "cargo.cve_accion","acciones.cve_accion")
                ->join("persona" , "cargo.cve_persona","persona.cve_persona")
                ->leftJoin("socios" , "cargo.cve_persona","socios.cve_persona")
                ->leftJoin("descuento" , "cargo.cve_cargo" , "descuento.cve_cargo")
                ->whereRaw("PERIOD_DIFF(?,CONCAT(RIGHT(cargo.periodo, 4) ,LEFT(cargo.periodo, 2))) >0",[$periodo])
                ->whereNull("cargo.idpago")
                ->where("cargo.cve_accion",">",0)
                ->select(
                "cargo.cve_cuota", 
                "cargo.concepto",
                "cargo.periodo",
                "cargo.fecha_cargo",
                "cargo.total",
                "persona.nombre",
                "persona.apellido_paterno",
                "persona.apellido_materno")
                ->selectRaw("IFNULL(descuento.monto,0) AS total_descuento")
                ->selectRaw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_name")
                ->selectRaw("IFNULL(socios.posicion,'-') AS posicion");
               

                if($cuota??false)
                { 
                    $query->where("cargo.cve_cuota",$cuota);
                   
                }
                              
                // dd($periodo);
                return $query->get();

        }
        catch(\Exception $e)
        {
            return $e;
        }
    }
    public static function getReporteGeneralAccionCargos2or3Detalle($cuota,$periodo,$cantidad)
    {
        try{
            
            /*
                SELECT 
                    cargo.cve_cuota, 
	                cargo.concepto,
	                GROUP_CONCAT(cargo.periodo) AS periodo,
	                GROUP_CONCAT(cast(cargo.fecha_cargo AS DATE )) AS fecha_cargo,
	                SUM(cargo.total) AS total,
	                SUM(IFNULL(descuento.monto,0)) AS total_descuento,
	                CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_name,
	                IFNULL(socios.posicion,'-') AS posicion,
	                persona.cve_persona,
	                persona.nombre,
	                persona.apellido_paterno,
	                persona.apellido_materno

                FROM cargo
                INNER JOIN acciones ON cargo.cve_accion=acciones.cve_accion
                INNER JOIN persona ON cargo.cve_persona=persona.cve_persona
                LEFT JOIN socios ON cargo.cve_persona=socios.cve_persona
                LEFT JOIN descuento ON cargo.cve_cargo = descuento.cve_cargo
                WHERE cargo.cve_cuota =1 AND PERIOD_DIFF(202401,CONCAT(RIGHT(cargo.periodo, 4) ,LEFT(cargo.periodo, 2))) >0 AND cargo.idpago IS NULL AND cargo.cve_accion>0
                GROUP BY cargo.cve_accion HAVING COUNT(cargo.cve_accion)=3
            */

                $query=DB::table("cargo")
                ->join("acciones" , "cargo.cve_accion","acciones.cve_accion")
                ->join("persona" , "cargo.cve_persona","persona.cve_persona")
                ->leftJoin("socios" , "cargo.cve_persona","socios.cve_persona")
                ->leftJoin("descuento" , "cargo.cve_cargo" , "descuento.cve_cargo")
                ->whereRaw("PERIOD_DIFF(?,CONCAT(RIGHT(cargo.periodo, 4) ,LEFT(cargo.periodo, 2))) >0",[$periodo])
                ->whereNull("cargo.idpago")
                ->where("cargo.cve_accion",">",0)
                ->where("cargo.cve_cuota",$cuota)
                ->groupBy("cargo.cve_accion")
                ->havingRaw("COUNT(cargo.cve_accion)=?",[$cantidad])
                ->select(
                "cargo.cve_cuota", 
                "cargo.concepto",
                "persona.nombre",
                "persona.apellido_paterno",
                "persona.apellido_materno")
                ->selectRaw("SUM(IFNULL(descuento.monto,0)) AS total_descuento")
                ->selectRaw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_name")
                ->selectRaw("IFNULL(socios.posicion,'-') AS posicion")
                ->selectRaw("GROUP_CONCAT(cargo.periodo) AS periodo")
                ->selectRaw("GROUP_CONCAT(cast(cargo.fecha_cargo AS DATE )) AS fecha_cargo")
                ->selectRaw("SUM(cargo.total) AS total");
                              
                return $query->get();

        }
        catch(\Exception $e)
        {
            return $e;
        }
    }

    public static function getReporteGeneralMovimeintosAccionDetalle($fecha_inicio,$fecha_fin,$movimiento,$tipo)
    {
        // dd($fecha_inicio);
        try{
            /*
                SELECT 
                    CONCAT(acciones.numero_accion,case acciones.clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' ELSE '' END) accion,
                    tipo_accion.nombre,
                    persona.nombre,
                    persona.apellido_paterno,
                    persona.apellido_materno,
                    persona.rfc        
                FROM acciones_historico 
                INNER JOIN acciones ON acciones_historico.cve_accion=acciones.cve_accion
                INNER JOIN tipo_accion ON acciones.cve_tipo_accion=tipo_accion.cve_tipo_accion
                INNER JOIN dueno ON acciones.cve_dueno=dueno.cve_dueno
                INNER JOIN persona ON dueno.cve_persona=persona.cve_persona
                WHERE  fecha_modificacion BETWEEN '2024-01-01 00:00:00' AND '2024-01-31 23:59:59' AND acciones.numero_accion<=1500

                #es cambio de dueño
                #AND estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno!=acciones.cve_dueno
                #es cambio de tipo accion 
                #AND (estatus_anterior=estatus_actual AND acciones_historico.cve_tipo_accion!=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno)
                #cambio accion de activa a bloqueada 
                #AND (estatus_anterior=1 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno)
                #cambio accion de activa a baja
                #AND (estatus_anterior=1 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno)
                #cambio de accion de bloqueada a activa
                #AND (estatus_anterior=2 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno)
                #cambio de accion de bloqueada a baja
                #AND (estatus_anterior=2 AND estatus_actual=3 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno)
                #cambio de accion baja a activa
                #AND (estatus_anterior=3 AND estatus_actual=1 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno)
                #cambio de accion baja a bloqueada
                #AND (estatus_anterior=3 AND estatus_actual=2 AND acciones_historico.cve_tipo_accion=acciones.cve_tipo_accion AND acciones_historico.cve_dueno=acciones.cve_dueno)

            */


                $query=DB::table("acciones_historico")
                ->join("acciones" , "acciones_historico.cve_accion","acciones.cve_accion")                
                ->join("tipo_accion" , "acciones_historico.cve_tipo_accion","tipo_accion.cve_tipo_accion")
                ->join("tipo_accion AS tipo_accion_act" , "acciones_historico.cve_tipo_accion_actual","tipo_accion_act.cve_tipo_accion")
                ->join("dueno" , "acciones_historico.cve_dueno","dueno.cve_dueno")
                ->join("dueno AS dueno_act" , "acciones_historico.cve_dueno_actual","dueno_act.cve_dueno")
                ->join("persona" , "dueno.cve_persona","persona.cve_persona")
                ->join("persona AS persona_act" , "dueno_act.cve_persona","persona_act.cve_persona")
                ->whereRaw("acciones_historico.fecha_modificacion BETWEEN ? AND ?",[$fecha_inicio,$fecha_fin])
                ->where("acciones_historico.numero_accion","<=",1500)
                ->select(
                    "tipo_accion.nombre AS tipo_accion_",
                    "tipo_accion_act.nombre AS tipo_accion_act",
                    "persona.nombre",
                    "persona.apellido_paterno",
                    "persona.apellido_materno",
                    "persona.rfc",
                    "persona_act.nombre AS nombre_act",
                    "persona_act.apellido_paterno AS apellido_paterno_act",
                    "persona_act.apellido_materno AS apellido_materno_act",
                    "persona_act.rfc AS rfc_act",
                    "acciones_historico.motivo_cambio"
                    )
                ->selectRaw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion_name");

               if(($movimiento??false)==1)
               {            
                $query->whereColumn("estatus_anterior","estatus_actual")->whereColumn("acciones_historico.cve_tipo_accion","acciones_historico.cve_tipo_accion_actual")->whereColumn("acciones_historico.cve_dueno","!=","acciones_historico.cve_dueno_actual");  
                if(collect([2,3,8])->some($tipo))
                {$query->where("acciones_historico.cve_tipo_accion",$tipo);}
               }
               
               else if(($movimiento??false)==2)  {             
                $query->whereColumn("estatus_anterior","estatus_actual")->whereColumn("acciones_historico.cve_tipo_accion","!=","acciones_historico.cve_tipo_accion_actual")->whereColumn("acciones_historico.cve_dueno","acciones_historico.cve_dueno_actual");
                if(collect([2,3,8])->some($tipo))
                {$query->where("acciones_historico.cve_tipo_accion",$tipo);}
               }
               
               else if(($movimiento??false)==3)   {                      
                $query->where("estatus_anterior",1)->where("estatus_actual",2)->whereColumn("acciones_historico.cve_tipo_accion","acciones_historico.cve_tipo_accion_actual")->whereColumn("acciones_historico.cve_dueno","acciones_historico.cve_dueno_actual");
                if(collect([2,3,8])->some($tipo))
                {$query->where("acciones_historico.cve_tipo_accion",$tipo);}
               }
               
               else if(($movimiento??false)==4)   {                      
                $query->where("estatus_anterior",1)->where("estatus_actual",3)->whereColumn("acciones_historico.cve_tipo_accion","acciones_historico.cve_tipo_accion_actual")->whereColumn("acciones_historico.cve_dueno","acciones_historico.cve_dueno_actual");
                if(collect([2,3,8])->some($tipo))
                {$query->where("acciones_historico.cve_tipo_accion",$tipo);}
               }
               
               else if(($movimiento??false)==5)   {                      
                $query->where("estatus_anterior",2)->where("estatus_actual",1)->whereColumn("acciones_historico.cve_tipo_accion","acciones_historico.cve_tipo_accion_actual")->whereColumn("acciones_historico.cve_dueno","acciones_historico.cve_dueno_actual");
                if(collect([2,3,8])->some($tipo))
                {$query->where("acciones_historico.cve_tipo_accion",$tipo);}
               }
               
               else if(($movimiento??false)==6)   {                      
                $query->where("estatus_anterior",2)->where("estatus_actual",3)->whereColumn("acciones_historico.cve_tipo_accion","acciones_historico.cve_tipo_accion_actual")->whereColumn("acciones_historico.cve_dueno","acciones_historico.cve_dueno_actual");
                if(collect([2,3,8])->some($tipo))
                {$query->where("acciones_historico.cve_tipo_accion",$tipo);}
               }

               else if(($movimiento??false)==7)   {                      
                $query->where("estatus_anterior",3)->where("estatus_actual",1)->whereColumn("acciones_historico.cve_tipo_accion","acciones_historico.cve_tipo_accion_actual")->whereColumn("acciones_historico.cve_dueno","acciones_historico.cve_dueno_actual");
                if(collect([2,3,8])->some($tipo))
                {$query->where("acciones_historico.cve_tipo_accion",$tipo);}
               }
               
               else if(($movimiento??false)==8)   {                      
                $query->where("estatus_anterior",3)->where("estatus_actual",2)->whereColumn("acciones_historico.cve_tipo_accion","acciones_historico.cve_tipo_accion_actual")->whereColumn("acciones_historico.cve_dueno","acciones_historico.cve_dueno_actual");
                if(collect([2,3,8])->some($tipo))
                {$query->where("acciones_historico.cve_tipo_accion",$tipo);}
               }
                //  dd($query->toSql());
                // return $query->count();
                return $query->get();

        }
        catch(\Exception $e)
        {
          return $e;
        }
    }

    public static function getAccionesByEstatus($id_estatus)
    {
      
        /*
            SELECT 
                CONCAT(numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS num_accion,
                tipo_accion.nombre AS tipo_accion_,
                dueno.rfc ,
                CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS detalle 
            FROM acciones
            INNER JOIN tipo_accion ON acciones.cve_tipo_accion=tipo_accion.cve_tipo_accion
            LEFT JOIN dueno ON acciones.cve_dueno = dueno.cve_dueno
            LEFT JOIN persona ON dueno.cve_persona=persona.cve_persona
            WHERE acciones.estatus=2
        */
         $acciones=DB::table("acciones")
        ->join("tipo_accion" , "acciones.cve_tipo_accion","tipo_accion.cve_tipo_accion")
        ->leftJoin("dueno" , "acciones.cve_dueno" , "dueno.cve_dueno")
        ->leftJoin("persona" , "dueno.cve_persona","persona.cve_persona")
        ->where("acciones.estatus",$id_estatus)
        ->where("acciones.numero_accion","<=",1500)
        ->select("tipo_accion.nombre AS tipo_accion_","dueno.rfc")
        ->selectRaw("CONCAT(numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS num_accion")
        ->selectRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) AS detalle");
      
            return $acciones->get();
        

    }

    public static function getSociosActivosDetalle()
    {
          /*
            SELECT 
                CONCAT(acciones.numero_accion,case acciones.clasificacion WHEN 1 THEN 'A' WHEN 1 THEN 'A' WHEN 1 THEN 'A' ELSE '' END ) AS accion,
                socios.posicion,
                persona.nombre,
                persona.apellido_paterno,
                persona.apellido_materno  
            FROM socios
            INNER JOIN acciones ON socios.cve_accion=acciones.cve_accion
            INNER JOIN  persona ON socios.cve_persona=persona.cve_persona
            WHERE acciones.numero_accion<=1500
        */
        $socios=DB::table("socios")
        ->join("acciones" , "socios.cve_accion","acciones.cve_accion")
        ->join("persona" , "socios.cve_persona","persona.cve_persona")
        ->where("acciones.numero_accion","<=",1500)
        ->select("socios.posicion",
        "persona.nombre",
        "persona.apellido_paterno",
        "persona.apellido_materno")
        ->selectRaw("CONCAT(acciones.numero_accion,case acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END ) AS accion");
        return $socios->get();
    }

    public static function getSociosMovimientosDetalle($fecha_inicio,$fecha_fin,$tipo)
    {
        /*
            SELECT 
                CONCAT(acciones.numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion ,
                socio_accion.nip,
                socios.posicion,
                persona.nombre,
                persona.apellido_paterno,
                persona.apellido_materno
            from socio_accion
            INNER JOIN socios ON socio_accion.cve_socio=socios.cve_socio
            INNER JOIN persona ON socios.cve_persona=persona.cve_persona
            INNER JOIN acciones ON socio_accion.cve_accion=acciones.cve_accion
            where socio_accion.fecha_hora_movimiento between '2024-01-01 00:00:00' and '2024-01-31 23:59:59'
            AND  socio_accion.movimiento=1
        */
        
        $socios=DB::table("socio_accion")
        ->join("socios" , "socio_accion.cve_socio","socios.cve_socio")
        ->join("persona" , "socios.cve_persona","persona.cve_persona")
        ->join("acciones" , "socio_accion.cve_accion","acciones.cve_accion")
        // ->where("acciones.numero_accion","<=",1500)
        ->whereRaw("socio_accion.fecha_hora_movimiento between ? and ?",[$fecha_inicio,$fecha_fin])
        ->whereRaw("CAST(socio_accion.movimiento AS unsigned)=?",[$tipo])
        ->select("socios.posicion",
        "persona.nombre",
        "persona.apellido_paterno",
        "persona.apellido_materno")
        ->selectRaw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END ) AS accion");       
        return $socios->get();
    }

    public static function  getHistoricoResumenAcciones()
    {
        return DB::table("resumen_acciones")->get();
    }
  
}