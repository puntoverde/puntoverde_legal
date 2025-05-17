<?php
namespace App\Controllers;
use App\DAO\DescuentoDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class DescuentoController extends Controller{

    public function __construct(){}

    public function getDescuento(Request $p){
        $this->validate($p,["numero_accion"=>"required|numeric","clasificacion"=>"required|numeric"]); 
        return DescuentoDAO::Descuento((object)$p->all());
    }

    public function getDeleteDescuento($cve, Request $p){
       $this->validate($p,["responsable_cancela"=>"required|numeric","motivo_cancelacion"=>"required"]);
       return DescuentoDAO::deleteDescuento($cve, (object)$p->all());
    }

    public function getDeleteDescuentoReporte (Request $p){
        $this->validate($p,["fecha_inicio"=>"required|date","fecha_fin"=>"required|date|after_or_equal:fecha_inicio"]);
        return DescuentoDAO::DescuentoReporte((object)$p->all());
    }
}


