<?php
namespace App\DAO;

use App\Entity\Cargo;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CargoDAO {


    public function __construct(){}


    public static function Cargo($p){
       try{
           return Cargo::join("persona","cargo.cve_persona","persona.cve_persona")
           ->join("acciones","cargo.cve_accion","acciones.cve_accion")
           ->where("acciones.numero_accion",$p->numero_accion)
           ->where("acciones.clasificacion",$p->clasificacion)
           ->whereNull ("cargo.idpago")
           ->select("acciones.numero_accion","acciones.clasificacion")
           ->addSelect(DB::raw("concat_ws(' ',persona.nombre, persona.apellido_paterno, persona.apellido_materno ) as nombre"))
           ->addSelect("cargo.concepto","cargo.total","cargo.cantidad","cargo.periodo","cargo.cve_cargo")
           ->get();

       }
       catch(\Exception $e){
       }
    }



    public static function deleteCargo($cve,$p){   
        try{

            return DB::transaction(function () use($cve, $p){

            DB::select('INSERT INTO 
            cancelar_cargo(cve_cancelar_cargo,cve_accion,cve_cuota,cve_persona,concepto,total,subtotal,iva,cantidad,periodo,responsable_carga,responsable_cancelar,fecha_cargo,recargo,motivo_cancelacion)
            SELECT cve_cargo,cve_accion,cve_cuota,cve_persona,concepto,total,subtotal,iva,cantidad,periodo,responsable_carga,?,fecha_cargo,recargo,?  
            FROM cargo WHERE cve_cargo =?',[$p->responsable_cancela,$p->motivo_cancelacion,$cve]);

            $cargo=Cargo::find($cve);

            $cve_accion=$cargo->cve_accion;

            $cargo->delete();


            $adeudo=DB::table('cargo')
            ->where('cve_accion',$cve_accion)
            ->whereIn('cve_cuota',[1,2,3,4,5])
            ->whereNull("idpago")
            ->selectRaw("COUNT(cve_cargo) AS adeudo")
            ->value("adeudo");

            if($adeudo==0)
            {
                DB::table("acciones")->where('cve_accion',$cve_accion)->update(["estatus"=>1,"motivo"=>"Se Elimino Cargos que Bloqueaban."]);
            }

            if($p->activa_accion??0)
            {
                DB::table("acciones")->where('cve_accion',$cve_accion)->update(["estatus"=>1,"motivo"=>"Se acepto activar accion cuando se elimino el cargo."]);
            }

            return 1;

            });
        }
        catch(\Exception $e){
            return 0;
        }
        
        // $cargo=Cargo::find($cve);
        // $cargo->delete();
    }



    public static function cargoReporte($p){
        try{
            $existeCargoCancelado=DB::table("cancelar_cargo")
            ->join("persona","persona.cve_persona","cancelar_cargo.responsable_cancelar")
            ->join("acciones","acciones.cve_accion","cancelar_cargo.cve_accion")
            ->whereRaw("cancelar_cargo.fecha_cancelacion >= '2021-01-01 00:00:01' AND  convert(cancelar_cargo.fecha_cancelacion,DATE) >= ? AND CONVERT(cancelar_cargo.fecha_cancelacion,DATE) <= ?",[$p->fecha_inicio,$p->fecha_fin])
            ->select("cancelar_cargo.concepto")
            ->addSelect("cancelar_cargo.periodo","cancelar_cargo.total","cancelar_cargo.fecha_cancelacion","cancelar_cargo.motivo_cancelacion")            
            ->addSelect(DB::raw("CONCAT(CONVERT(numero_accion ,char),case clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' else '' end) accion"))
            ->addSelect(DB::raw("concat_ws(' ',persona.nombre, persona.apellido_paterno, persona.apellido_materno) as personaCancela"));
            

            
            return $existeCargoCancelado
            ->get();


        }catch(\Exception $e){
            return 0;
        }
    }



    public static function getEstatusAccionByCargo($id){
        $periodo=Carbon::now()->format("m-Y"); 
        $estatus=Cargo::join("acciones","cargo.cve_accion","acciones.cve_accion")->where("cve_cargo",$id)->where("cargo.periodo",$periodo)->value("acciones.estatus")??0;
        return $estatus;
     }




}