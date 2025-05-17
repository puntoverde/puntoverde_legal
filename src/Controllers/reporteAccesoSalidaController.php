<?php

namespace App\Controllers;
use App\DAO\reporteAccesoSalidaDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class reporteAccesoSalidaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    public function otorgarSalidas(Request $req){
        return reporteAccesoSalidaDAO::otorgarSalidas();
    }

   
    
}
