<?php

namespace App\Controllers;
use App\DAO\ReporteHistoricoAccionesDAO;
use App\Entity\Locker;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class ReporteHistoricoAccionesController extends Controller
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
        return response()->json(ReporteHistoricoAccionesDAO::getAccion((object)$req->all()));
    }

    public function getDuenos($id){
        return ReporteHistoricoAccionesDAO::getDuenos($id);
    }


    public function getSocios($id){
        return ReporteHistoricoAccionesDAO::getSocios($id);
    }

    public function getCargos($id)
    {
       return ReporteHistoricoAccionesDAO::getCargos($id);
    }
    
    public function getTipoAndEstatus($id)
    {
       return ReporteHistoricoAccionesDAO::getTipoAndEstatus($id);
    }
    
}
