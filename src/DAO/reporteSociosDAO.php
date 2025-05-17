<?php
namespace App\DAO;
use App\Entity\Accion;
use App\Entity\Socio;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class reporteSociosDAO
{
	private $sql;
	private $sta;
    
    // function parseFecha($f)
    // {
    //     $arr = explode("/", $f);
    //     return $arr[2]."-".$arr[1]."-".$arr[0];
    // }
    function __construct(){
        
    }
    public static function listaSocios($p)
    {   


        /*
        
            SELECT 
	            persona.cve_persona,
	            persona.nombre,
	            persona.apellido_paterno,
	            persona.apellido_materno,
	            socios.cve_socio ,
	            CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END),
	            acciones.cve_accion AS cve_accion_accion,
	            (SELECT  GROUP_CONCAT(accd.cve_accion) FROM persona AS pd INNER JOIN dueno ON pd.cve_persona=dueno.cve_persona INNER JOIN acciones AS accd ON accd.cve_dueno=dueno.cve_dueno WHERE accd.cve_accion=acciones.cve_accion and pd.nombre=persona.nombre AND pd.apellido_paterno=persona.apellido_paterno AND pd.apellido_materno=persona.apellido_materno  LIMIT 1) AS cve_accion_is_dueno,
	            socios.posicion,
	            socios.cve_accion,
	            (SELECT  group_concat(DISTINCT CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END)) FROM persona AS pd INNER JOIN dueno ON pd.cve_persona=dueno.cve_persona INNER JOIN acciones ON acciones.cve_dueno=dueno.cve_dueno where pd.nombre=persona.nombre AND pd.apellido_paterno=persona.apellido_paterno AND pd.apellido_materno=persona.apellido_materno  LIMIT 1) AS acciones_dueno
            FROM acciones 
            RIGHT JOIN socios ON acciones.cve_accion=socios.cve_accion
            RIGHT JOIN persona ON socios.cve_persona=persona.cve_persona 
            WHERE socios.cve_accion IS NOT NULL;
        
        */
 
        
        $subquery_dueno_acciones=DB::table("persona AS pd")
        ->join("dueno" , "pd.cve_persona","dueno.cve_persona")
        ->join("acciones" , "acciones.cve_dueno","dueno.cve_dueno")
        ->whereColumn("pd.nombre","persona.nombre")
        ->whereColumn("pd.apellido_paterno","persona.apellido_paterno")
        ->whereColumn("pd.apellido_materno","persona.apellido_materno")
        ->selectRaw("GROUP_CONCAT(DISTINCT CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END))")
        ->limit(1);       
        
        $subquery_is_dueno=DB::table("persona AS pd")
        ->join("dueno" , "pd.cve_persona","dueno.cve_persona")
        ->join("acciones AS accd" , "accd.cve_dueno","dueno.cve_dueno")
        ->whereColumn("accd.cve_accion","acciones.cve_accion")
        ->whereColumn("pd.nombre","persona.nombre")
        ->whereColumn("pd.apellido_paterno","persona.apellido_paterno")
        ->whereColumn("pd.apellido_materno","persona.apellido_materno")
        ->selectRaw("GROUP_CONCAT(accd.cve_accion)")
        ->limit(1);

        try{
            $sql = Accion::rightJoin("socios","acciones.cve_accion","socios.cve_accion")
            ->rightJoin("persona","socios.cve_persona", "persona.cve_persona");

            if($p->nombre??false) $sql->whereRaw("CONCAT(nombre, ' ', apellido_paterno, ' ', apellido_materno) LIKE ?",['%'.$p->nombre.'%']);
            if(($p->numero_accion ?? false) && is_numeric($p->clasificacion ?? false))
              {
                $sql->where('numero_accion',$p->numero_accion)->where('clasificacion',$p->clasificacion);
              }
            if(($p->estatus??0)==1) $sql->whereNotNull("socios.cve_accion");
            else  $sql->whereNull("socios.cve_accion");
          

            $sql->select(
                "persona.cve_persona",
                "persona.nombre", 
                "persona.apellido_paterno",
                "persona.apellido_materno",
                "socios.cve_socio",                
                "socios.posicion",
                "socios.cve_accion",
                "acciones.estatus AS estatus_accion")
                ->selectRaw("CONCAT(acciones.numero_accion,CASE acciones.clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) as accion_name")                
                ->selectRaw("CAST(IF(socios.estatus + persona.estatus IS NULL,0,socios.estatus + persona.estatus) AS UNSIGNED) AS estatus_")                      
                ->addSelect(["cve_accion_is_dueno"=>$subquery_is_dueno])                
                ->addSelect(["acciones_dueno"=>$subquery_dueno_acciones]);                
              
            return $sql->get();
            
        }
            catch(\Exception $e){

        }	
          
    }
    
    
    public static function cargarDetalles($id)
    {
        try{

            return  Accion::rightJoin("socios","acciones.cve_accion","socios.cve_accion")
            ->join("persona","socios.cve_persona", "persona.cve_persona")
            ->join("direccion","socios.cve_direccion","direccion.cve_direccion")
            ->leftJoin("colonia","direccion.cve_colonia","colonia.cve_colonia")
            ->leftJoin("municipio","municipio.cve_municipio","colonia.cve_municipio")
            ->leftJoin("parentescos","socios.cve_parentesco","parentescos.cve_parentesco")
            ->leftJoin("tipo_accion","acciones.cve_tipo_accion","tipo_accion.cve_tipo_accion")
            ->leftJoin("socio_accion","socios.cve_socio","socio_accion.cve_socio")
            ->where("socios.cve_socio",$id)
            ->select("socios.cve_socio",DB::raw("concat_ws(' ',persona.nombre, persona.apellido_paterno, persona.apellido_materno ) as nombre"),"acciones.numero_accion", "acciones.clasificacion")
            ->addSelect("socios.posicion","parentescos.nombre as parentesco","direccion.calle","direccion.numero_exterior")
            ->addSelect("colonia.nombre as colonia", "municipio.nombre as municipio")
            ->addSelect("socios.foto","tipo_accion.nombre as tipo_accion") 
            ->selectRaw("CONCAT(numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion")
            ->addSelect("persona.sexo","persona.fecha_nacimiento","persona.curp","persona.rfc","socios.fecha_ingreso_club")
            ->selectRaw("GROUP_CONCAT(CONCAT_WS(' ',CONVERT(socio_accion.fecha_hora_movimiento,DATE),socio_accion.movimiento)) AS historico")
            ->first();

        }
        catch(\Exception $e){

        }
    }

    public static function cargarAdeudos($id){        
        try{
            return DB::table("cargo")
            ->join("persona","cargo.cve_persona","persona.cve_persona")
            ->join("socios","persona.cve_persona","socios.cve_persona")
            ->where("socios.cve_socio",85)
            ->whereNull("idpago")
            ->select("cargo.concepto","cargo.total","cargo.periodo")
            ->first();
        }
        catch(\Exception $e){
        }        
    }

    

    public static function toggleSocio($id,$p)
    {
        try{

            // var_dump ($p->numero_accion);
             $cve_accion = Accion::where("numero_accion",$p->numero_accion)->where("clasificacion",$p->clasificacion)->value("cve_accion");
             $cve_persona = Socio::where("cve_socio",$id)->value("cve_persona");
            
             DB::table("socios")->where("cve_socio",$id)->update(["cve_accion"=>$cve_accion,"estatus"=>1,"fecha_reingreso"=>Carbon::now()]);
             DB::table("persona")->where("cve_persona",$cve_persona)->update(["estatus"=>1]);
            
            return 1;
        }
        catch(\Exception $e){

        }

    }

    public static function getFotoSocio($id)
    {
        try {
            return DB::table("socios")->where("socios.cve_socio", $id)->value("foto_socio");
        } catch (\Exception $th) {
        }
    }


    public static function updateFotoSocio($id,$foto)
    {
        try{
            
            // return DB::table("socios")->where("cve_socio",$id)->update(["foto"=>$foto]);
            return DB::table("socios")->where("cve_socio",$id)->update(["foto_socio"=>$foto]);

        }
        catch(\Exception $e){

        }

    }

}
