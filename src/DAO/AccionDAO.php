<?php
namespace App\DAO;
use App\Entity\Accion;
use Illuminate\Support\Facades\DB;


class AccionDAO {

    public function __construct(){}

    public static function getAcciones($p){

        $acciones= DB::table("acciones AS acc")
        ->leftJoin("tipo_accion AS tac","acc.cve_tipo_accion","tac.cve_tipo_accion")
        ->SelectRaw("CONCAT(acc.numero_accion,'-',acc.clasificacion) AS nom_completo")
        ->addSelect('acc.cve_accion','acc.numero_accion', 'acc.clasificacion', 'acc.estatus','tac.nombre as tipo_accion') 
        ->addSelect('acc.cve_tipo_accion', 'acc.fecha_alta','acc.fecha_adquisicion', 'acc.fecha_baja', 'cve_dueno');
            
        if($p->numero_accion ?? false){ $acciones->where("acc.numero_accion",$p->numero_accion);}

        if(is_numeric($p->clasificacion ?? false )){ $acciones->where("acc.clasificacion",$p->clasificacion);}

        if($p->tipo_accion ?? false){$acciones->where("acc.cve_tipo_accion",$p->tipo_accion);}

        if($p->estatus ?? false){$acciones->where("acc.estatus",$p->cve_tipo_accion);}

        return $acciones->get();

   }

   public static function getAccionById($id){
    return Accion::find($id);
 }


   public static function updateAccion($id,$p){
      
      $accion=Accion::find($id);
      if($p->estatus??0)$accion->estatus=$p->estatus;
      if($p->cve_tipo_accion??false)$accion->cve_tipo_accion=$p->cve_tipo_accion;
      if($p->fecha_adquisicion??false)$accion->fecha_adquisicion=$p->fecha_adquisicion;
      if($p->motivo??false)$accion->motivo=$p->motivo;
      if($p->persona_motivo??false)$accion->persona_motivo=$p->persona_motivo;
      $ok=$accion->save();

      return $ok;

   }

   public static function agregarCuotaActivacion($id)
   {
      try{
      $id_persona=DB::table("acciones")->join("dueno","acciones.cve_dueno","dueno.cve_dueno")->where("cve_accion",$id)->value("cve_persona");
      $periodo=date("m-Y");

      $exist=DB::table("cargo")->where("cve_accion",$id)->where("cve_cuota",5)->whereNull("idpago")->exists();

      if(!$exist)
      {        
         DB::select(
            "INSERT INTO 
                       cargo(cve_accion,cve_cuota,cve_persona,concepto,total,subtotal,iva,periodo,responsable_carga)
            SELECT 
                       ?,cve_cuota,?,cuota,precio,ROUND(precio/1.16,2),ROUND((precio/1.16)*.16,2),?,0 
            FROM cuota WHERE cve_cuota=5",[$id,$id_persona,$periodo]);
               return 1;   
         }
      }

      
      catch(\Exception $e){         
         return 0;
      }
   }


    public static function updateFechasAccion($p,$id_colaborador){
      
      $accion=Accion::find($p->cve_accion);
      
      DB::table("accion_bitacora_fechas_libro")->insert(["id_colaborador"=>$id_colaborador,"fecha_alta"=>$p->fecha_alta,"fehca_adquisicion"=>$p->fecha_adquisicion,"fehca_adquisicion_anterior"=>$accion->fecha_adquisicion,"fecha_alta_anterior"=>$accion->fecha_alta]);
            
      $accion->fecha_alta=$p->fecha_alta;
      $accion->fecha_adquisicion=$p->fecha_adquisicion;      
      
      $ok=$accion->save();


      return $ok;

   }
}