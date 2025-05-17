<?php
namespace App\DAO;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ValidacionSolicitudNuevoDuenoDAO {

    public function __construct(){}
    /**
     * 
     */
    
    public static function getSociosIngresoNuevoAll($p)
    {
        /*
            SELECT 
                dueno.cve_dueno,
  	            CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion,  	            
  	            persona.nombre,
  	            persona.apellido_paterno,
  	            persona.apellido_materno,
  	            persona.sexo,
  	            persona.fecha_nacimiento,
  	            persona.estado_civil
            FROM dueno 
            INNER JOIN persona  ON dueno.cve_persona=persona.cve_persona
            INNER JOIN direccion ON dueno.cve_direccion=direccion.cve_direccion
            INNER JOIN colonia ON direccion.cve_colonia=colonia.cve_colonia
            INNER JOIN municipio ON colonia.cve_municipio=municipio.cve_municipio
            INNER JOIN estado ON municipio.cve_estado=estado.cve_estado
            INNER JOIN pais ON estado.cve_pais=pais.cve_pais            
            INNER JOIN acciones ON dueno.cve_dueno=acciones.cve_dueno
            where acciones.numero_accion<=1500 and dueno.fecha_validacion is null
        */

        try{

             $query=DB::table("dueno")
            ->join("persona"  , "dueno.cve_persona","persona.cve_persona")
            ->join("direccion" , "dueno.cve_direccion","direccion.cve_direccion")
            ->join("colonia" , "direccion.cve_colonia","colonia.cve_colonia")
            ->join("municipio" , "colonia.cve_municipio","municipio.cve_municipio")
            ->join("estado" , "municipio.cve_estado","estado.cve_estado")
            ->join("pais" , "estado.cve_pais","pais.cve_pais")            
            ->join("acciones" , "dueno.cve_dueno","acciones.cve_dueno")
            ->select(
                "dueno.cve_dueno",               
                "persona.nombre",
  	            "persona.apellido_paterno",
  	            "persona.apellido_materno",
  	            "persona.sexo",
  	            "persona.fecha_nacimiento",
  	            "persona.estado_civil"
            )
            ->selectRaw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion")
            ->where("acciones.numero_accion","<=",1500)
            ->whereNull("dueno.fecha_validacion");
            
        
            if(((bool)($p->fecha_inicio??''))==true && ((bool)($p->fecha_fin??''))==true)
            {
                $query->whereRaw("dueno.fecha_validacion BETWEEN ? AND ?",[$p->fecha_inicio , $p->fecha_fin])->orderBy("dueno.fecha_validacion");
            }
            else{
                $query->whereNull("dueno.fecha_validacion");
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
  	            CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion,
  	            persona.nombre,
  	            persona.apellido_paterno,
  	            persona.apellido_materno,
  	            persona.sexo,
  	            persona.fecha_nacimiento,
  	            persona.curp,
  	            persona.rfc,
  	            persona.estado_civil,
  	            persona.cve_pais,
  	            dueno.telefono,
  	            dueno.celular,
  	            dueno.foto,
  	            direccion.calle,
  	            direccion.numero_exterior,
  	            direccion.numero_interior,
  	            colonia.nombre AS colonia_name,
  	            colonia.cp,
  	            municipio.nombre AS municipio_name,
  	            estado.nombre AS estado_name,
  	            estado.abreviacion,
  	            pais.nombre AS pais_name  		
            FROM dueno 
            INNER JOIN persona  ON dueno.cve_persona=persona.cve_persona
            INNER JOIN direccion ON dueno.cve_direccion=direccion.cve_direccion
            INNER JOIN colonia ON direccion.cve_colonia=colonia.cve_colonia
            INNER JOIN municipio ON colonia.cve_municipio=municipio.cve_municipio
            INNER JOIN estado ON municipio.cve_estado=estado.cve_estado
            INNER JOIN pais ON estado.cve_pais=pais.cve_pais            
            INNER JOIN acciones ON dueno.cve_dueno=acciones.cve_dueno
        
        */
        // dd($id);
        try {
           
            return DB::table("dueno")
            ->join("persona"  , "dueno.cve_persona","persona.cve_persona")
            ->join("direccion" , "dueno.cve_direccion","direccion.cve_direccion")
            ->join("colonia" , "direccion.cve_colonia","colonia.cve_colonia")
            ->join("municipio" , "colonia.cve_municipio","municipio.cve_municipio")
            ->join("estado" , "municipio.cve_estado","estado.cve_estado")
            ->join("pais" , "estado.cve_pais","pais.cve_pais")            
            ->join("acciones" , "dueno.cve_dueno","acciones.cve_dueno")
            ->select(
                "persona.nombre",
  	            "persona.apellido_paterno",
  	            "persona.apellido_materno",
  	            "persona.sexo",
  	            "persona.fecha_nacimiento",
  	            "persona.curp",
  	            "persona.rfc",
  	            "persona.estado_civil",
  	            "persona.cve_pais",  	            
  	            "dueno.telefono",
  	            "dueno.celular",
  	            "dueno.foto",
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
            ->where("dueno.cve_dueno",$id)
            ->first();
           

        } catch (\Exception $e) {            
            dd($e);
        }
    }

    public static function setValidacionUsiarioNuevoIngreso($ids,$clave,$id_usuario)
    {
        
        try{
            if(DB::table("usuario")->where("usuario.id_usuario",$id_usuario)->where("usuario.contrasena",$clave)->exists())
            {
                return DB::table("dueno")->whereIn("cve_dueno",$ids)->update(["fecha_validacion"=>Carbon::now()]);
            }
            else{
                return 0;
            }
        }
        catch(\Exception $e)
        {
            return 0;
        }
    }

    public static function getFotoSocio($id)
    {
        try {
            return DB::table("socios")->where("socios.cve_socio", $id)->value("foto_socio");
        } catch (\Exception $th) {
        }
    }

  
}