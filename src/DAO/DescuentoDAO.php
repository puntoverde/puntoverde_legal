<?php
namespace App\DAO;

use App\Entity\Descuento;
use Illuminate\Support\Facades\DB;

class DescuentoDAO {


    public function __construct(){}


    public static function Descuento($p){
       try{
           return Descuento::join("cargo","descuento.cve_cargo","cargo.cve_cargo")
           ->join("acciones","cargo.cve_accion","acciones.cve_accion")
           ->where("acciones.numero_accion",$p->numero_accion)
           ->where("acciones.clasificacion",$p->clasificacion)
           ->whereNull ("cargo.idpago")
           ->select("descuento.iddescuento","descuento.monto")
           ->addSelect("descuento.descripcion","descuento.fecha_aplicacion","cargo.concepto","cargo.total","cargo.periodo","cargo.cantidad")
           ->addSelect("acciones.numero_accion","acciones.clasificacion")
           ->get();

       }
       catch(\Exeption $e){
       }
    }



    public static function deleteDescuento($cve,$p){   
        try{

            return DB::transaction(function () use($cve, $p){

            DB::select('INSERT INTO 
            cancelar_descuento(cve_cancelar_descuento, cve_cargo, cve_persona_otorga, monto, descripcion, fecha_aplicacion, cve_persona_elimina, motivo_cancelacion)        
            SELECT iddescuento, cve_cargo, persona_otorga, monto, descripcion, fecha_aplicacion,?,?
            FROM descuento WHERE iddescuento =?',[$p->responsable_cancela,$p->motivo_cancelacion,$cve]);

            $descuento=Descuento::find($cve);

            $descuento->delete();

            return 1;

            });
        }
        catch(\Exeption $e){
            return 0;
        }
        
        $cargo=Cargo::find($cve);
        $cargo->delete();
    }

    public static function DescuentoReporte($p){
        try{
            $existeCargo=DB::table("cancelar_descuento")
            ->leftJoin("persona as persona1","persona1.cve_persona","cancelar_descuento.cve_persona_otorga")
            ->join("persona as persona2","persona2.cve_persona","cancelar_descuento.cve_persona_elimina")
            ->join("cargo","cargo.cve_cargo","cancelar_descuento.cve_cargo")
            ->join("acciones","acciones.cve_accion","cargo.cve_accion")
            ->whereRaw("convert(cancelar_descuento.fecha_eliminacion,DATE) >= ? AND CONVERT(cancelar_descuento.fecha_eliminacion,DATE) <= ?",[$p->fecha_inicio,$p->fecha_fin])
            ->select("cancelar_descuento.monto","cancelar_descuento.descripcion as motivoDescuento")
            ->addSelect("cancelar_descuento.fecha_aplicacion","cancelar_descuento.motivo_cancelacion","cancelar_descuento.fecha_eliminacion")
            ->addSelect(DB::raw("concat_ws(' ',persona1.nombre, persona1.apellido_paterno, persona1.apellido_materno) as personaOtorga"))
            ->addSelect(DB::raw("concat_ws(' ',persona2.nombre, persona2.apellido_paterno, persona2.apellido_materno) as personaElimina"))
            ->addSelect("cargo.concepto","cargo.periodo","cargo.total","acciones.numero_accion","acciones.clasificacion")
            ->addSelect(DB::raw("1 as cargoExiste"));

            $existeCancelarCargo=DB::table("cancelar_descuento")
            ->leftJoin("persona as persona1","persona1.cve_persona","cancelar_descuento.cve_persona_otorga")
            ->join("persona as persona2","persona2.cve_persona","cancelar_descuento.cve_persona_elimina")
            ->join("cancelar_cargo","cancelar_cargo.cve_cancelar_cargo","cancelar_descuento.cve_cargo")
            ->join("acciones","acciones.cve_accion","cancelar_cargo.cve_accion")
            ->whereRaw("convert(cancelar_descuento.fecha_eliminacion,DATE) >= ? AND CONVERT(cancelar_descuento.fecha_eliminacion,DATE) <= ?",[$p->fecha_inicio,$p->fecha_fin])
            ->select("cancelar_descuento.monto","cancelar_descuento.descripcion as motivoDescuento")
            ->addSelect("cancelar_descuento.fecha_aplicacion","cancelar_descuento.motivo_cancelacion","cancelar_descuento.fecha_eliminacion")
            ->addSelect(DB::raw("concat_ws(' ',persona1.nombre, persona1.apellido_paterno, persona1.apellido_materno) as personaOtorga"))
            ->addSelect(DB::raw("concat_ws(' ',persona2.nombre, persona2.apellido_paterno, persona2.apellido_materno) as personaElimina"))
            ->addSelect("cancelar_cargo.concepto","cancelar_cargo.periodo","cancelar_cargo.total","acciones.numero_accion","acciones.clasificacion")
            ->addSelect(DB::raw("0 as cargoExiste"));

            //return $existeCargo
            
            return $existeCargo->union($existeCancelarCargo)
            ->get();


        }catch(\Exeption $e){
            return 0;
        }
    }
}