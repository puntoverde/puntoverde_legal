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

class AccionistaDAO {

    public function __construct(){}
    /**
     * 
     */
    public static function insertAccionista($p)
    {   
        

       return DB::transaction(function () use ($p){

        $colonia=Colonia::find($p->cve_colonia);

        $persona=new Persona();
        $persona->nombre=$p->nombre;
        $persona->apellido_paterno=$p->apellido_paterno;
        $persona->apellido_materno=$p->apellido_materno;
        $persona->sexo=$p->sexo;
        $persona->fecha_nacimiento=$p->fecha_nacimiento;
        $persona->cve_pais=$p->cve_pais;
        $persona->curp=$p->curp;
        $persona->rfc=$p->rfc;
        $persona->estado_civil=$p->estado_civil;
        $persona->estatus=1;
        $persona->save();

        $direccion=new Direccion();
        $direccion->calle=$p->calle;
        $direccion->numero_exterior=$p->numero_exterior;
        $direccion->numero_interior=$p->numero_interior;
        $direccion->colonia()->associate($colonia);
        $direccion->save();
      
        $accionista=new Accionista();
        $accionista->celular=$p->celular;
        $accionista->telefono=$p->telefono;
        $accionista->expediente=$p->expediente;
        $accionista->estatus=1;
        
        $accionista->persona()->associate($persona);
        $accionista->direccion()->associate($direccion);
        
        $accionista->save();

        $accion=Accion::find($p->cve_accion);
        $accion->fecha_adquisicion=$p->fecha_adquisicion;
        $accion->accionista()->associate($accionista);
        $accion->save();

        return $accionista->cve_dueno;

        });


    }

    public static function updateAccionista($id,$p){
       
        return DB::transaction(function () use ($id,$p){

            $colonia=Colonia::find($p->cve_colonia);

            $accionista=Accionista::find($id);
            $accionista->celular=$p->celular;
            $accionista->telefono=$p->telefono;
            $accionista->expediente=$p->expediente;
            $accionista->estatus=1;
           

            $persona=Persona::find($accionista->cve_persona);
            $persona->nombre=$p->nombre;
            $persona->apellido_paterno=$p->apellido_paterno;
            $persona->apellido_materno=$p->apellido_materno;
            $persona->sexo=$p->sexo;
            $persona->fecha_nacimiento=$p->fecha_nacimiento;
            $persona->cve_pais=$p->cve_pais;
            $persona->curp=$p->curp;
            $persona->rfc=$p->rfc;
            $persona->estado_civil=$p->estado_civil;
            $persona->estatus=1;
            $persona->save();
    
    
    
            try{
                $direccion=Direccion::findOrFail($accionista->cve_direccion);
                $direccion->calle=$p->calle;
                $direccion->numero_exterior=$p->numero_exterior;
                $direccion->numero_interior=$p->numero_interior;
                $direccion->colonia()->associate($colonia);
                $direccion->save();
            }
            catch(ModelNotFoundException $e){
                    $direccion=new Direccion();
                    $direccion->calle=$p->calle;
                    $direccion->numero_exterior=$p->numero_exterior;
                    $direccion->numero_interior=$p->numero_interior;
                    $direccion->colonia()->associate($colonia);
                    $direccion->save();
                    $accionista->direccion()->associate($direccion);
            }
            $accionista->save(); 
            
            $accion = Accion::find($p->cve_accion);
            $accion->fecha_adquisicion=$p->fecha_adquisicion;
            $accion->save();
            

            return 1;

        });

    }

    public static function findAccionista($id){
      
        $accionista=Accionista::join('acciones','dueno.cve_dueno','acciones.cve_dueno')
        ->join('persona','persona.cve_persona','dueno.cve_persona')
        ->leftJoin('direccion' , 'direccion.cve_direccion','dueno.cve_direccion')
        ->leftJoin('colonia' , 'direccion.cve_colonia' , 'colonia.cve_colonia')
        ->leftJoin('municipio' ,'municipio.cve_municipio', 'colonia.cve_municipio')
        ->leftJoin('estado' , 'estado.cve_estado', 'municipio.cve_estado')
        ->select('dueno.cve_dueno','dueno.cve_persona','dueno.celular','dueno.telefono','dueno.rfc')
        ->addSelect(DB::raw('IFNULL(dueno.cve_direccion,0) AS cve_direccion'))
        ->addSelect('persona.nombre','persona.apellido_paterno','persona.apellido_materno',DB::raw('CONVERT(persona.sexo, SIGNED) AS sexo'),'persona.fecha_nacimiento','persona.cve_pais','persona.curp','persona.rfc')
        ->addSelect('direccion.cve_colonia','direccion.calle','direccion.numero_exterior','direccion.numero_interior')
        ->addSelect('colonia.cve_municipio','colonia.nombre as colonia','colonia.tipo','colonia.cp')
        ->addSelect('acciones.cve_accion','acciones.numero_accion','acciones.clasificacion','acciones.cve_tipo_accion','acciones.fecha_alta','acciones.fecha_baja','acciones.fecha_adquisicion')
        ->addSelect('municipio.nombre as municipio','estado.nombre as estado','dueno.foto')
        ->where('dueno.cve_dueno',$id);
        return $accionista->first();      
    }

    public static function getAccionistas()
    {
        try {
            return Accionista::leftJoin('acciones','dueno.cve_dueno','acciones.cve_dueno')
            ->join('persona','dueno.cve_persona','persona.cve_persona')
            ->groupBy('dueno.cve_dueno')
            ->orderBy('persona.nombre')
            ->orderBy('dueno.cve_dueno')
            ->select('dueno.cve_dueno AS id',DB::raw("CONCAT(persona.nombre, ' ', persona.apellido_paterno,' ',persona.apellido_materno) AS nombre"))
            ->get();            
        } catch (\Exception $e) {            
            return [];
        }
    }

    public static function CambiarDueno($p)
    {        
        try {
            $accion=Accion::find($p->cve_accion);
            $accion->cve_dueno=$p->cve_dueno;
            $accion->fecha_adquisicion=Carbon::now();
            $accion->save();
        } catch (\Exception $e) {return false;} 
    }

    public static function addFoto($id,$foto){
         $accionista=Accionista::find($id);
         $accionista->foto=$foto;
         $accionista->save();
         return 1;
    }

    public static function deleteFoto($id){
        $accionista=Accionista::find($id);
        $accionista->foto=null;//es nullo para eliminar la relaccion con la foto
        $accionista->save();
        return 1;
   }


    public static function getDocumentos($id,$p){
        return DB::table('documento_dueno')
        ->rightJoin('documento',function($join) use($id,$p){
         $join->on('documento_dueno.cve_documento','documento.cve_documento')->where('cve_dueno',$id)->where('cve_accion',$p->cve_accion);})
         ->join("documento_proceso","documento.cve_documento","documento_proceso.cve_documento")       
        ->select('documento.cve_documento','documento_dueno.cve_documento_dueno')
        ->addSelect('documento.documento','documento.tipo','documento_dueno.ruta','documento_dueno.estatusDocumento')
        ->where("proceso","ACCIONISTA")
        ->groupBy("documento.cve_documento")
        ->get();
    }

    public static function setDocumento($id,$p){        
        $dueno=Accionista::find($id);
        $dueno->documentos()->detach($p->cve_documento);
        $dueno->documentos()->attach($p->cve_documento,['cve_accion'=>$p->cve_accion,'ruta'=>$p->documento,'estatusDocumento'=>1,'nombre'=>'nom']);
        return $dueno->documentos()->wherePivot('cve_documento',$p->cve_documento)->first()->pivot->cve_documento_socio;
    }

    public static function deleteDocumento($id,$cve_documento){        
        $dueno=Accionista::find($id);
        $dueno->documentos()->detach($cve_documento);
    }

    //inserta accionista pero en forma de baja y solo lo liga al historico
    public static function insertAccionistaHistorico($p)
    {   
        
       if(Persona::where("curp",$p->curp)->exists())
       {
           return ["existe"=>"curp"];
       }
       else
       {
        return DB::transaction(function () use ($p){

            $colonia=Colonia::find($p->cve_colonia);
    
            $persona=new Persona();
            $persona->nombre=$p->nombre;
            $persona->apellido_paterno=$p->apellido_paterno;
            $persona->apellido_materno=$p->apellido_materno;
            $persona->sexo=$p->sexo;
            $persona->fecha_nacimiento=$p->fecha_nacimiento;
            $persona->cve_pais=$p->cve_pais;
            $persona->curp=$p->curp;
            $persona->rfc=$p->rfc;
            $persona->estado_civil=$p->estado_civil;
            $persona->estatus=1;
            $persona->save();
    
            $direccion=new Direccion();
            $direccion->calle=$p->calle;
            $direccion->numero_exterior=$p->numero_exterior;
            $direccion->numero_interior=$p->numero_interior;
            $direccion->colonia()->associate($colonia);
            $direccion->save();
          
            $accionista=new Accionista();
            $accionista->celular=$p->celular;
            $accionista->telefono=$p->telefono;
            $accionista->expediente=$p->expediente;
            $accionista->estatus=1;
            
            $accionista->persona()->associate($persona);
            $accionista->direccion()->associate($direccion);
            
            $accionista->save();
    
            DB::table("acciones_historico")
            ->insert([
                "cve_accion"=>$p->cve_accion,
                "numero_accion"=>"",
                "clasificacion"=>0,
                "cve_dueno"=>$accionista->cve_dueno,
                "cve_tipo_accion"=>"",
                "fecha_alta"=>"",
                "fecha_baja"=>"",
                "fecha_adquisicion"=>"",
                "estatus_anterior"=>"",
                "estatus_actual"=>"",
                "observacion"=>"",
                "fecha_modificacion"=>"",
                "motivo_cambio"=>""]);
    
            return $accionista->cve_dueno;
    
            });
       }

    }


    public static function getLibroAccionistas()
    {
        // no accion,dueño,rfc,curp,fecha movimiento,estatus,autorizado
        try {
            return Accionista::join('acciones','dueno.cve_dueno','acciones.cve_dueno')
            ->join('persona','dueno.cve_persona','persona.cve_persona')
            // ->groupBy('dueno.cve_dueno')
            // ->orderBy("persona.apellido_paterno","asc")
            // ->orderBy("persona.apellido_materno","asc")
            // ->orderBy("persona.nombre","asc")
            // ->orderBy('persona.nombre')
            // ->orderBy('dueno.cve_dueno')
            ->orderBy('acciones.numero_accion')
            ->orderBy('acciones.clasificacion')
            ->select('acciones.cve_accion','dueno.cve_dueno AS id','persona.nombre','persona.apellido_paterno' ,'persona.apellido_materno','persona.rfc','persona.curp','acciones.estatus')
            ->selectRaw("CONCAT(numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS no_accion")
            ->where("acciones.numero_accion","<=",1500)
            ->get();            
        } catch (\Exception $e) {            
            return [];
        }
    }
    public static function getLibroAccionistasHistorico($id_accion)
    {
    
            // SELECT 
            //     persona.nombre,
            //     persona.apellido_paterno,
            //     persona.apellido_materno,
            //     persona.rfc,
            //     persona.curp,
            //     persona_actual.nombre AS nombre_actual,
            //     persona_actual.apellido_paterno AS apellido_paterno_actual,
            //     persona_actual.apellido_materno AS apellido_materno_actual,
            //     persona_actual.rfc,
            //     persona_actual.curp,
            //     acciones_historico.fecha_modificacion
            // FROM acciones_historico
            // INNER JOIN dueno ON acciones_historico.cve_dueno=dueno.cve_dueno
            // INNER JOIN dueno AS dueno_actual ON acciones_historico.cve_dueno_actual=dueno_actual.cve_dueno
            // INNER JOIN persona ON dueno.cve_persona=persona.cve_persona
            // INNER JOIN persona AS persona_actual ON dueno_actual.cve_persona=persona_actual.cve_persona
            // WHERE acciones_historico.cve_accion=200 AND estatus_anterior=estatus_actual AND cve_tipo_accion=cve_tipo_accion_actual AND acciones_historico.cve_dueno!=acciones_historico.cve_dueno_actual

        try {
            return DB::table('acciones_historico')
            ->join("dueno" , "acciones_historico.cve_dueno","dueno.cve_dueno")
            ->join("dueno AS dueno_actual" , "acciones_historico.cve_dueno_actual","dueno_actual.cve_dueno")
            ->join('persona','dueno.cve_persona','persona.cve_persona')
            ->join("persona AS persona_actual" , "dueno_actual.cve_persona","persona_actual.cve_persona")            
            ->select(
                "persona.nombre",
                "persona.apellido_paterno",
                "persona.apellido_materno",
                "persona.rfc",
                "persona.curp",
                "persona_actual.nombre AS nombre_actual",
                "persona_actual.apellido_paterno AS apellido_paterno_actual",
                "persona_actual.apellido_materno AS apellido_materno_actual",
                "persona_actual.rfc AS rfc_actual",
                "persona_actual.curp AS curp_actual",
                "acciones_historico.fecha_modificacion"
            )
            ->where("acciones_historico.cve_accion",$id_accion)
            ->whereColumn("estatus_anterior","estatus_actual")
            ->whereColumn("cve_tipo_accion","cve_tipo_accion_actual")
            ->whereColumn("acciones_historico.cve_dueno","!=","acciones_historico.cve_dueno_actual")
            // ->orderBy("persona.apellido_paterno","asc")
            // ->orderBy("persona.apellido_materno","asc")
            // ->orderBy("persona.nombre","asc")
            ->orderBy("acciones_historico.fecha_modificacion")
            ->get();            
        } catch (\Exception $e) {            
            return [];
        }
    }
}