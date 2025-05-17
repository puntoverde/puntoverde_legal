<?php

namespace App\Controllers;

use App\DAO\HistorialMedicoDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class HistorialMedicoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function allPersonas(Request $req)
    {
        $reglas = ["nombre" => "required"];
        $this->validate($req, $reglas);
        try {
            $data = HistorialMedicoDAO::allPersonas($req->input('nombre'));
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function findPersonaById($id)
    {
        try {
            $data = HistorialMedicoDAO::findPersonaById($id);
            return response()->json($data);
        } catch (\Exception $e) {
            dd($e);
        }

    }

    public function registrarPercance(Request $req)
    {
        $reglas = [
            "id_persona" => "required",
            "encargado" => "required",
            "descripcion" => "required",
            "fecha_registro"=>"required",
            "hora_inicio" => "required",
            "hora_fin" => "required",
        ];

        $this->validate($req, $reglas);

        try {
            $data = HistorialMedicoDAO::registrarPercance((object)$req->all());
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function actualizarPercance(Request $req,$id){
        $reglas = [
            "observacion" => "required",
            "factores_riesgo" => "required",
            "resultado" => "required",
            "motivo" => "required_if:resultado,3",
        ];

        $this->validate($req, $reglas);

        try {
            $data = HistorialMedicoDAO::actualizarPercance($id,(object)$req->all());
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getPercances($id){
        return HistorialMedicoDAO::getPercances($id);
    }


    public function getReporteHistorialMedico($id){
        return HistorialMedicoDAO::getReporteHistorialMedico($id);
    }

}
