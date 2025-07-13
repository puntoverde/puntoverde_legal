<?php

namespace App\Controllers;
use App\DAO\AccionDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class AccionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    public function getAcciones(Request $req){
        return AccionDAO::getAcciones((object)$req->all());
    }

    public function getAccionById($id){
        return AccionDAO::getAccionById($id);
    }
        

    public function updateAccion($id,Request $req){
        return AccionDAO::updateAccion($id,(object)$req->all());
    }    

    public function agregarCuotaActivacion($id){
        return AccionDAO::agregarCuotaActivacion($id);
    }
    
    public function updateFechasAccion(Request $req){
        $reglas = ["cve_accion" => "required", "fecha_alta" => "required", "fecha_adquisicion" => "required"];

        $this->validate($req, $reglas);
        $id_colaborador=$req->get("id_colaborador");
        return AccionDAO::updateFechasAccion((object)$req->all(),$id_colaborador);
    }
}
