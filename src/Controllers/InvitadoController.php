<?php

namespace App\Controllers;

use App\DAO\InvitadoDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class InvitadoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }


    public function getInvitados(Request $req)
    {
        return InvitadoDAO::getInvitados($req->input('search_invitado'));
    }

    public function getInvitado($id)
    {
        return InvitadoDAO::getInvitadoById($id);
    }

    public function getHistoricoInvitado($id)
    {
        return InvitadoDAO::getHistoricoInvitado($id);
    }

    public function getSociosInvitaByNombre(Request $req)
    {
        return InvitadoDAO::getSociosInvitaByNombre($req->input('nombre'));
    }

    public function getListaSociosInvitan(Request $req)
    {
        return InvitadoDAO::getListaSociosInvitan($req->input('numero_accion'),$req->input('clasificacion'));
    }

    public function getAccionesLibresInvitados()
    {
        return InvitadoDAO::getAccionesLibresInvitados();
    }

    public function setInvitado(Request $req)
    {
        $reglas = [
            "nombre" => "required", 
            "fecha_nac" => "required",  
            // "posicion" => "required", 
            "cve_accion" => "required", 
            // "profesion" => "required", 
            // "parentesco" => "required"
        ];

        $this->validate($req, $reglas);
        return InvitadoDAO::insertInvitado((object)$req->all());
    }


    public function reingresoInvitado($id, Request $req)
    {
        // $reglas = ["nombre" => "required", "fecha_nacimiento" => "required", "cve_profesion" => "required", "cve_parentesco" => "required", "cve_persona" => "required", "cve_direccion" => "required"];
        // $this->validate($req, $reglas);
        return InvitadoDAO::reingresoInvitado($id, (object)$req->all());
    }

    public function getInvitadosCargos()
    {
        return InvitadoDAO::getInvitadosCargos();
    }
    
    public function deleteInvitado(Request $req)
    {
        $reglas = [
            "id_socio" => "required",
            "id_invitado" => "required",
        ];

        $this->validate($req, $reglas);
        return InvitadoDAO::deleteInvitado($req->input("id_socio"),$req->input("id_invitado"));
    }
    
}