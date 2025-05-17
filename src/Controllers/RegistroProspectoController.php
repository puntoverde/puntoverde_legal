<?php

namespace App\Controllers;
use App\DAO\RegistroProspectoDAO;
use App\Mail\SolicitudMail;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\Mail;

class RegistroProspectoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    public function getAccion(Request $req){              
        $reglas = ["numero_accion"=>"required|integer","clasificacion"=>"required|integer"];        
        $this->validate($req, $reglas);
        return response()->json(RegistroProspectoDAO::getAccion((object)$req->all()));
    }

    public function saveSolicitud(Request $req)
    {
        $reglas=[
        "interesado"=>"required",
        "correo"=>"required",                        
        "tipo"=>"required",
        "cantidad_usuarios"=>"required",                        
        "cve_accion"=>"required",
        "accion"=>"required"];
        $this->validate($req,$reglas);

        try{
        $correo=$req->input("correo");
        
        $data=(object)RegistroProspectoDAO::saveSolicitud((object)$req->all());        
        Mail::to($correo)->send(new SolicitudMail($correo,$data->folio,$data->clave,$data->fecha));
        return response()->json($data);
        }
        catch(\Exception $e){
            dd($e);
        }

    }

    public function sendEmail(Request $req)    
    {
        $reglas=[
            "correo"=>"required|email",
            "folio"=>"required",                        
            "clave"=>"required",
            "fecha"=>"required"];
            $this->validate($req,$reglas);
            $correo=$req->input("correo");
            $folio=$req->input("folio");
            $clave=$req->input("clave");
            $fecha=$req->input("fecha");

        Mail::to($correo)->send(new SolicitudMail($correo,$folio,$clave,$fecha));
    }


    public function getSolicitudes(Request $req){

        // $reglas=[
        //     "correo"=>"required|email",
        //     "folio"=>"required",                        
        //     "clave"=>"required",
        //     "fecha"=>"required"];
            // $this->validate($req,$reglas);

        return RegistroProspectoDAO::getSolicitudes((object)$req);
    }







    public function getDuenos($id){
        return RegistroProspectoDAO::getDuenos($id);
    }


    public function getSocios($id){
        return RegistroProspectoDAO::getSocios($id);
    }

    public function getCargos($id)
    {
       return RegistroProspectoDAO::getCargos($id);
    }
    
    public function getTipoAndEstatus($id)
    {
       return RegistroProspectoDAO::getTipoAndEstatus($id);
    }
    
}
