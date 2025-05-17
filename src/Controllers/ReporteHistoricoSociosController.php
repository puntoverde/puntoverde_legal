<?php

namespace App\Controllers;
use App\DAO\ReporteHistoricoSociosDAO;
use App\Entity\Locker;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class ReporteHistoricoSociosController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    public function getSocio(Request $req){        
        return response()->json(ReporteHistoricoSociosDAO::getSocio($req->input('nombre')));
    }

    public function getHistorico($id){
        return ReporteHistoricoSociosDAO::getHistorico($id);
    }

    public function getSocioDatos($id){        
        return response()->json(ReporteHistoricoSociosDAO::getSocioDatos($id));
    }

    public function setHistorico(Request $req){        
        return ReporteHistoricoSociosDAO::setHistorico((object)$req->all());
    }
    
}
