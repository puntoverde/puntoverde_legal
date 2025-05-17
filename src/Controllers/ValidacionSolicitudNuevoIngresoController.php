<?php

namespace App\Controllers;
use App\DAO\ValidacionSolicitudNuevoIngresoDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class ValidacionSolicitudNuevoIngresoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    public function getSociosIngresoNuevoAll(Request $req){
        return ValidacionSolicitudNuevoIngresoDAO::getSociosIngresoNuevoAll((object)$req->all());
    }

    public function getSociosIngresoNuevo($id){
        $data=ValidacionSolicitudNuevoIngresoDAO::getSociosIngresoNuevo($id);
        return response()->json($data);
    }

    public function getViewFoto(Request $req)
    {    $foto=$req->input('foto');
         $img=file_get_contents("../upload/$foto");
         return response($img)->header('Content-type','image/png');
    }

    public function setValidacionUsiarioNuevoIngreso($id)
    {      
       return ValidacionSolicitudNuevoIngresoDAO::setValidacionUsiarioNuevoIngreso($id);    
    }

}
