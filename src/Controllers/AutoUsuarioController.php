<?php

namespace App\Controllers;
use App\DAO\AutoUsuarioDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class AutoUsuarioController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    public function getAutoUsuario(Request $req){
        // return response()->json(AutoUsuarioDAO::getAutoUsuario((object)$req->all()))->setEncodingOptions(JSON_NUMERIC_CHECK);
        return AutoUsuarioDAO::getAutoUsuario((object)$req->all());
    }

    public function bajaAutoUsuario($id)
    {
        return AutoUsuarioDAO::bajaAutoUsuario($id);
    }
    
    public function changeColorAutoUsuario(Request $req,$id)
    {
        $color=$req->input("color");
        return AutoUsuarioDAO::changeColorAutoUsuario($id,$color);
    }
    
    public function changePlacasAutoUsuario(Request $req,$id)
    {
        $placas=$req->input("placa");
        return AutoUsuarioDAO::changePlacasAutoUsuario($id,$placas);
    }


}
