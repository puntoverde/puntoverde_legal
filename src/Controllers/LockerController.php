<?php

namespace App\Controllers;
use App\DAO\LockerDAO;
use App\Entity\Locker;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class LockerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    public function crearLocker(Request $req)
    {
        return LockerDAO::crearLocker((object)$req->all());
    }

    public function ModificarLocker(Request $req,$id)
    {
        return LockerDAO::ModificarLocker($id,(object)$req->all());
    }


    public function getLockerById($id)
    {
        return response()->json(LockerDAO::getLockerById($id));
    }


    public function getListaLockers(Request $req)
    {
        return LockerDAO::getListaLockers((object)$req);
    }

    public function getLockers(Request $req){
        return LockerDAO::getLockers((object)$req->all());
    }

    public function getHistoricoLocker($id){
        return LockerDAO::getHistoricoLocker($id);
    }

    public function asignarLocker($id,Request $req){
        $reglas = ["cve_accion"=>"required|integer", 
                   "cve_persona"=>"required|integer",
                   "total"=>"required|numeric", 
                   "concepto"=>"required",
                   "fecha_inicio"=>"required|date",
                   "fecha_fin"=>"required|date",
                   "tipo"=>"required"];        
        $this->validate($req, $reglas);
       return LockerDAO::asignarLocker($id,(object)$req->all());
    }

    public function cancelarRentaLocker($id,Request $req){

        $reglas = ["cve_persona"=>"required|integer","motivo_cancelacion"=>"required","cve_persona_cancela"=>"required"];        
        $this->validate($req, $reglas);
       return LockerDAO::cancelarRentaLocker($id,(object)$req->all());
    }

    public function getSocios(Request $req){
        return LockerDAO::getSocios((object)$req->all());
    }

    public function getCuota()
    {
       return json_encode(LockerDAO::getCuota());
    }

    public function regularizadoNoRegularizado($id,Request $req){
        return LockerDAO::regularizadoNoRegularizado($id,(object)$req->all());
    }

    public function uploadContrato(Request $req)
    {
        if ($req->hasFile('contrato')) {
            $file = $req->file('contrato');
            $temp = explode(".", $file->getClientOriginalName());
            $directorio='../upload/';
            $filename = $req->input('cve_locker_socio') . '.pdf';
            if ($file->isValid()) {
             try{$file->move($directorio,$filename);
            return $filename;
            }
             catch(\Exception $e){return $e;}
            }
            // else return 'ocurrio un error con el contrato ';
            return response('ocurrio un error con el contrato',204);
        }
        else {
            return response('no existe el Documento..',204);
        }
        
    }

    public function AsignarContrato($id,Request $req){
        $reglas = [
             "cve_persona"=>"required|integer",
             "folio"=>"required", 
             "documento"=>"required",        
             "observaciones"=>"required"];        
        $this->validate($req, $reglas);
        return LockerDAO::AsignarContrato($id,(object)$req->all());
    }

    public function viewContrato(Request $req)
    {
        $archivo=$req->input('file');
        $pdf=file_get_contents("../upload/$archivo");
        return response($pdf)->header('Content-type','application/pdf');
    }
    

    public function getEdificios()
    {
        return LockerDAO::getEdificios();
    }

    public function getListaLockerDisponibles()
    {
        return LockerDAO::getListaLockerDisponibles();
    }

    public function asignarLockerMasivo(Request $req){
        // $reglas = ["cve_accion"=>"required|integer", 
        //            "cve_persona"=>"required|integer",
        //            "total"=>"required|numeric", 
        //            "concepto"=>"required",
        //            "fecha_inicio"=>"required|date",
        //            "fecha_fin"=>"required|date",
        //            "tipo"=>"required"];        
        // $this->validate($req, $reglas);
       return LockerDAO::asignarLockerMasivo($req->all());
    }

    

    public function EditarAsignacionLocker($id,Request $req){
        $reglas = ["cve_persona"=>"required|integer",
                //    "total"=>"required|numeric", 
                //    "concepto"=>"required",
                //    "fecha_inicio"=>"required|date",
                //    "fecha_fin"=>"required|date",
                ];        
        $this->validate($req, $reglas);
       return LockerDAO::EditarAsignacionLocker($id,(object)$req->all());
    }


    public function getDuenos(Request $req){
        return LockerDAO::getDuenos((object)$req->all());
    }

    public function EditarDuenoLocker($id,Request $req){
        $reglas = ["cve_persona"=>"required|integer",
                //    "total"=>"required|numeric", 
                //    "concepto"=>"required",
                //    "fecha_inicio"=>"required|date",
                //    "fecha_fin"=>"required|date",
                ];        
        $this->validate($req, $reglas);
       return LockerDAO::EditarDuenoLocker($id,(object)$req->all());
    }

    public function EditarUltimoNumeroLocker($id,Request $req){
        $reglas = ["numero_locker"=>"required|integer"];        
        $this->validate($req, $reglas);
       return LockerDAO::EditarUltimoNumeroLocker($id,(object)$req->all());
    }

    public function EditarObservacionAsignacion($id,Request $req){
        $reglas = ["observacion"=>"required"];        
        $this->validate($req, $reglas);
       return LockerDAO::EditarObservacionAsignacion($id,(object)$req->all());
    }
    
    public function agregar_permuta(Request $req){
        $reglas = ["cve_locker_uno"=>"required","cve_locker_dos"=>"required"];        
        $this->validate($req, $reglas);
       return LockerDAO::agregar_permuta((object)$req->all());
    }

    public function liberaLocker(Request $req){
        $reglas = ["id_locker"=>"required","id_persona"=>"required","motivo"=>"required"];        
        $this->validate($req, $reglas);
       return LockerDAO::liberaLocker((object)$req->all());
    }

    public function historicoLiberacion($id){                
       return LockerDAO::historicoLiberacion($id);
    }

    public function reporteHistoricoLiberacion(Request $req)
    {
        return LockerDAO::reporteHistoricoLiberacion($req);
    }
    

}
