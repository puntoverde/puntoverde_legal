<?php

namespace App\Controllers;
use App\DAO\ReporteGeneralDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ReporteGeneralController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    public function getReporteGeneral(Request $req){

        $periodo=$req->input("periodo");
        $periodo_db=Carbon::createFromFormat("m-Y",$periodo)->format("Ym");        
        $dias_total=Carbon::createFromFormat("m-Y",$periodo)->daysInMonth;
        $fecha_inicio=Carbon::createFromFormat("m-Y",$periodo)->day(1)->hour(0)->minute(0)->second(0)->toDateTimeString();
        $fecha_fin= Carbon::createFromFormat("m-Y",$periodo)->day($dias_total)->hour(23)->minute(59)->second(59)->toDateTimeString();       

        return ReporteGeneralDAO::getReporteGeneral($fecha_inicio,$fecha_fin,$periodo_db);
    }
    
    public function getReporteGeneralDetalle(Request $req){

        $cuotas=$collection = Str::of($req->input("cuotas"))->explode(',')->all();
        $pagado=$req->input("pagado");
        $periodo=$req->input("periodo");      
        $dias_total=Carbon::createFromFormat("m-Y",$periodo)->daysInMonth;
        $fecha_inicio=Carbon::createFromFormat("m-Y",$periodo)->day(1)->hour(0)->minute(0)->second(0)->toDateTimeString();
        $fecha_fin= Carbon::createFromFormat("m-Y",$periodo)->day($dias_total)->hour(23)->minute(59)->second(59)->toDateTimeString();       

        return ReporteGeneralDAO::getReporteGeneralDetalle($fecha_inicio,$fecha_fin,$cuotas,$pagado);
    }
    
    public function getReporteGeneralCargosAnterioresDetalle(Request $req){

        $cuota=$req->input("cuota");
        $periodo=$req->input("periodo");      
        $periodo_db=Carbon::createFromFormat("m-Y",$periodo)->format("Ym");   
        $dias_total=Carbon::createFromFormat("m-Y",$periodo)->daysInMonth;
        $fecha_inicio=Carbon::createFromFormat("m-Y",$periodo)->day(1)->hour(0)->minute(0)->second(0)->toDateTimeString();
        $fecha_fin= Carbon::createFromFormat("m-Y",$periodo)->day($dias_total)->hour(23)->minute(59)->second(59)->toDateTimeString();       

        return ReporteGeneralDAO::getReporteGeneralCargosAnterioresDetalle($fecha_inicio,$fecha_fin,$cuota,$periodo_db);
    }
    
    
    public function getReporteGeneralCargosAnterioresFaltantesDetalle(Request $req){

        
        $cuota=$req->input("cuota");
        $periodo=$req->input("periodo");      
        $periodo_db=Carbon::createFromFormat("m-Y",$periodo)->format("Ym");   
        
        return ReporteGeneralDAO::getReporteGeneralCargosAnterioresFaltantesDetalle($cuota,$periodo_db);
    }


    public function getReporteGeneralAccionCargos2or3Detalle(Request $req){

        
        $cuota=$req->input("cuota");
        $periodo=$req->input("periodo");      
        $cantidad=$req->input("cantidad");      
        $periodo_db=Carbon::createFromFormat("m-Y",$periodo)->format("Ym");   
        
        return ReporteGeneralDAO::getReporteGeneralAccionCargos2or3Detalle($cuota,$periodo_db,$cantidad);
    }

    public function getReporteGeneralMovimeintosAccionDetalle(Request $req){

        
        $movimiento=$req->input("movimiento");
        $tipo=$req->input("tipo");        
        $periodo=$req->input("periodo");        
        $dias_total=Carbon::createFromFormat("m-Y",$periodo)->daysInMonth;
        $fecha_inicio=Carbon::createFromFormat("m-Y",$periodo)->day(1)->hour(0)->minute(0)->second(0)->toDateTimeString();
        $fecha_fin= Carbon::createFromFormat("m-Y",$periodo)->day($dias_total)->hour(23)->minute(59)->second(59)->toDateTimeString();   
        
        return ReporteGeneralDAO::getReporteGeneralMovimeintosAccionDetalle($fecha_inicio,$fecha_fin,$movimiento,$tipo);
    }

    public function getAccionesByEstatus(Request $req)
    {
        return ReporteGeneralDAO::getAccionesByEstatus($req->input('estatus'));
    }
    
    public function getSociosActivosDetalle(Request $req)
    {
        return ReporteGeneralDAO::getSociosActivosDetalle();
    }
   
    public function getSociosMovimientosDetalle(Request $req)
    {
    
        $tipo=$req->input("tipo");        
        $periodo=$req->input("periodo");        
        $dias_total=Carbon::createFromFormat("m-Y",$periodo)->daysInMonth;
        $fecha_inicio=Carbon::createFromFormat("m-Y",$periodo)->day(1)->hour(0)->minute(0)->second(0)->toDateTimeString();
        $fecha_fin= Carbon::createFromFormat("m-Y",$periodo)->day($dias_total)->hour(23)->minute(59)->second(59)->toDateTimeString(); 
        return ReporteGeneralDAO::getSociosMovimientosDetalle($fecha_inicio,$fecha_fin,$tipo);
    }

    public function getHistoricoResumenAcciones()
    {
        return ReporteGeneralDAO::getHistoricoResumenAcciones();
    }

}
