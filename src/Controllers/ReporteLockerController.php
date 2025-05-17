<?php

namespace App\Controllers;
use App\DAO\ReporteLockerDAO;
use App\Entity\Locker;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class ReporteLockerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){}

    public function getEdificios(Request $req){
        return ReporteLockerDAO::getEdificios();
    }

    public function getReporteLockers(Request $req){
        return ReporteLockerDAO::getReporteLockers((object)$req->all());
    }

    public function getReporteLockersFull(Request $req){
        return ReporteLockerDAO::getReporteLockersFull((object)$req->all());
    } 
    
    public function getTotales(Request $req){
        return ReporteLockerDAO::getTotales((object)$req->all());
    } 

    public function getEstadisticasCards(Request $req)
    {
        return ReporteLockerDAO::getEstadisticasCards((object)$req->all());
    }

    public function getEstadisticaRentados(Request $req)
    {
        return ReporteLockerDAO::getEstadisticaRentados($req->input("periodo")); 
    }

    public function getEstadisticaNoRenueva(Request $req)
    {
        return ReporteLockerDAO::getEstadisticaNoRenueva($req->input("periodo")); 
    }

    public function getEstadisticaPerteneceClub(Request $req)
    {
        return ReporteLockerDAO::getEstadisticaPerteneceClub($req->input("periodo")); 
    }
    
    public function getEstadisticaPerteneceExterno(Request $req)
    {
        return ReporteLockerDAO::getEstadisticaPerteneceExterno($req->input("periodo")); 
    }
    
    public function getEstadisticaCargoOrPagos(Request $req)
    {
        return ReporteLockerDAO::getEstadisticaCargoOrPagos($req->input("periodo")); 
    }
    
    public function getEstadisticaLibres(Request $req)
    {
        return ReporteLockerDAO::getEstadisticaLibres($req->input("periodo")); 
    }
}
