<?php

namespace App\Controllers;
use App\DAO\ObservacionesUsuariosDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class ObservacionesUsuariosController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    public function getAcciones(Request $req){
        return ObservacionesUsuariosDAO::getAcciones($req->input("numero_accion"),$req->input("clasificacion"));
    }

    public function getUsuariosByName(Request $req){
        return ObservacionesUsuariosDAO::getUsuariosByName($req->input("search"));
    }

    public function showObservaciones($id,Request $req){
        return ObservacionesUsuariosDAO::showObservaciones($id);
    }    

    public function crearObservacion(Request $req){
        return ObservacionesUsuariosDAO::crearObservacion((object)$req->all());
    }
}
