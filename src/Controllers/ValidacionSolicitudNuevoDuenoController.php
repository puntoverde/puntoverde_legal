<?php

namespace App\Controllers;
use App\DAO\ValidacionSolicitudNuevoDuenoDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class ValidacionSolicitudNuevoDuenoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    public function getSociosIngresoNuevoAll(Request $req){
        return ValidacionSolicitudNuevoDuenoDAO::getSociosIngresoNuevoAll((object)$req->all());
    }

    public function getSociosIngresoNuevo($id){
        
        $data=ValidacionSolicitudNuevoDuenoDAO::getSociosIngresoNuevo($id);
        return response()->json($data);
    }

    public function getViewFoto(Request $req)
    {    
        $foto=$req->input('foto');
        $img=file_get_contents("../upload/$foto");
        return response($img)->header('Content-type','image/png');
        // $cve_socio=$req->input("foto");
        // $img= ValidacionSolicitudNuevoDuenoDAO::getFotoSocio($cve_socio);                            
        // return response($img)->header('Content-type','image/png');
    }

    public function setValidacionUsiarioNuevoIngreso(Request $req)
    {      
        $clave=$req->input("clave");
        $ids_duenos=$req->input("ids_dueno");        
        $id_usuario=$req->get("id_usuario");
       return ValidacionSolicitudNuevoDuenoDAO::setValidacionUsiarioNuevoIngreso($ids_duenos,$clave,$id_usuario);    
    }

}