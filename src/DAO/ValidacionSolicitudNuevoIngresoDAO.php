<?php
namespace App\DAO;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ValidacionSolicitudNuevoIngresoDAO {

    public function __construct(){}
    /**
     * 
     */
    
    public static function getSociosIngresoNuevoAll($p)
    {
        /*
            SELECT 
                socios.cve_socio,
  	            CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion,
  	            socios.posicion,
  	            persona.nombre,
  	            persona.apellido_paterno,
  	            persona.apellido_materno,
  	            persona.sexo,
  	            persona.fecha_nacimiento,
  	            persona.estado_civil,
  	            profesion.nombre AS profesion_name,
  	            parentescos.nombre AS parentesco_name
            FROM socios 
            INNER JOIN persona  ON socios.cve_persona=persona.cve_persona
            INNER JOIN direccion ON socios.cve_direccion=direccion.cve_direccion
            INNER JOIN colonia ON direccion.cve_colonia=colonia.cve_colonia
            INNER JOIN municipio ON colonia.cve_municipio=municipio.cve_municipio
            INNER JOIN estado ON municipio.cve_estado=estado.cve_estado
            INNER JOIN pais ON estado.cve_pais=pais.cve_pais
            INNER JOIN profesion ON socios.cve_profesion=profesion.cve_profesion
            INNER JOIN parentescos ON socios.cve_parentesco=parentescos.cve_parentesco
            INNER JOIN acciones ON socios.cve_accion=acciones.cve_accion
        */

        try{

             $query=DB::table("socios")
            ->join("persona"  , "socios.cve_persona","persona.cve_persona")
            ->join("direccion" , "socios.cve_direccion","direccion.cve_direccion")
            ->join("colonia" , "direccion.cve_colonia","colonia.cve_colonia")
            ->join("municipio" , "colonia.cve_municipio","municipio.cve_municipio")
            ->join("estado" , "municipio.cve_estado","estado.cve_estado")
            ->join("pais" , "estado.cve_pais","pais.cve_pais")
            ->join("profesion" , "socios.cve_profesion","profesion.cve_profesion")
            ->join("parentescos" , "socios.cve_parentesco","parentescos.cve_parentesco")
            ->join("acciones" , "socios.cve_accion","acciones.cve_accion")
            ->select(
                "socios.cve_socio",               
                "persona.nombre",
  	            "persona.apellido_paterno",
  	            "persona.apellido_materno",
  	            "persona.sexo",
  	            "persona.fecha_nacimiento",
  	            "persona.estado_civil",
  	            "profesion.nombre AS profesion_name",
  	            "parentescos.nombre AS parentesco_name",
                "socios.fecha_aceptacion",
                "socios.fecha_alta"            
            )
            ->selectRaw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion")
            ->selectRaw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END,'-', socios.posicion) AS nip");
        
            if(((bool)($p->fecha_inicio??''))==true && ((bool)($p->fecha_fin??''))==true)
            {
                $query->whereRaw("socios.fecha_aceptacion BETWEEN ? AND ?",[$p->fecha_inicio , $p->fecha_fin])->orderBy("socios.fecha_aceptacion");
            }
            else{
                $query->whereNull("socios.fecha_aceptacion");
            }            
            // return $query->toSql();
            return $query->get();
        }
        catch(\Exception $e){

        }
    }
    
    public static function getSociosIngresoNuevo($id)
    {

        /* 
            SELECT 
  	            CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END,'-',socios.posicion) AS accion,
  	            persona.nombre,
  	            persona.apellido_paterno,
  	            persona.apellido_materno,
  	            persona.sexo,
  	            persona.fecha_nacimiento,
  	            persona.curp,
  	            persona.rfc,
  	            persona.estado_civil,
  	            profesion.nombre AS profesion_name,
  	            parentescos.nombre AS parentesco_name,
  	            persona.cve_pais,
  	            socios.correo_electronico,
  	            socios.telefono,
  	            socios.celular,
  	            socios.foto,
  	            socios.observaciones,
  	            socios.capacidad,
  	            direccion.calle,
  	            direccion.numero_exterior,
  	            direccion.numero_interior,
  	            colonia.nombre AS colonia_name,
  	            colonia.cp,
  	            municipio.nombre AS municipio_name,
  	            estado.nombre AS estado_name,
  	            estado.abreviacion,
  	            pais.nombre AS pais_name  		
            FROM socios 
            INNER JOIN persona  ON socios.cve_persona=persona.cve_persona
            INNER JOIN direccion ON socios.cve_direccion=direccion.cve_direccion
            INNER JOIN colonia ON direccion.cve_colonia=colonia.cve_colonia
            INNER JOIN municipio ON colonia.cve_municipio=municipio.cve_municipio
            INNER JOIN estado ON municipio.cve_estado=estado.cve_estado
            INNER JOIN pais ON estado.cve_pais=pais.cve_pais
            INNER JOIN profesion ON socios.cve_profesion=profesion.cve_profesion
            INNER JOIN parentescos ON socios.cve_parentesco=parentescos.cve_parentesco
            INNER JOIN acciones ON socios.cve_accion=acciones.cve_accion
        
        */

        try {
           
            return DB::table("socios")
            ->join("persona"  , "socios.cve_persona","persona.cve_persona")
            ->join("direccion" , "socios.cve_direccion","direccion.cve_direccion")
            ->join("colonia" , "direccion.cve_colonia","colonia.cve_colonia")
            ->join("municipio" , "colonia.cve_municipio","municipio.cve_municipio")
            ->join("estado" , "municipio.cve_estado","estado.cve_estado")
            ->join("pais" , "estado.cve_pais","pais.cve_pais")
            ->join("profesion" , "socios.cve_profesion","profesion.cve_profesion")
            ->join("parentescos" , "socios.cve_parentesco","parentescos.cve_parentesco")
            ->join("acciones" , "socios.cve_accion","acciones.cve_accion")
            ->select(
                "persona.nombre",
  	            "persona.apellido_paterno",
  	            "persona.apellido_materno",
  	            "persona.sexo",
  	            "persona.fecha_nacimiento",
  	            "persona.curp",
  	            "persona.rfc",
  	            "persona.estado_civil",
  	            "profesion.nombre AS profesion_name",
  	            "parentescos.nombre AS parentesco_name",
  	            "persona.cve_pais",
  	            "socios.correo_electronico",
  	            "socios.telefono",
  	            "socios.celular",
  	            "socios.foto",
  	            "socios.observaciones",
  	            "socios.capacidad",
  	            "direccion.calle",
  	            "direccion.numero_exterior",
  	            "direccion.numero_interior",
  	            "colonia.nombre AS colonia_name",
  	            "colonia.cp",
  	            "municipio.nombre AS municipio_name",
  	            "estado.nombre AS estado_name",
  	            "estado.abreviacion",
  	            "pais.nombre AS pais_name"
            )
            ->selectRaw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion")
            ->selectRaw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END,'-',socios.posicion) AS nip")
            ->where("socios.cve_socio",$id)
            ->first();
           

        } catch (\Exception $e) {            
            return [];
        }
    }

    public static function setValidacionUsiarioNuevoIngreso($id)
    {
        try{
           return DB::table("socios")->where("cve_socio",$id)->update(["fecha_aceptacion"=>Carbon::now()]);
        }
        catch(\Exception $e)
        {
            return 0;
        }
    }

  
}