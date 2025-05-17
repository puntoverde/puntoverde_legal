<?php
namespace App\Controllers;
use App\DAO\ApartadoEstatusDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class ApartadoEstatusController extends Controller
{
    public function __construct(){}


    public function getApartadosEstatus(Request $p){
        $this->validate($p,["fecha_inicio"=>"required|date"]); 
        return ApartadoEstatusDAO::getApartadosTenis($p->input("fecha_inicio"));
    }

}