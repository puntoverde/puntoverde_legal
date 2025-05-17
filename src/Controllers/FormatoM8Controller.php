<?php

namespace App\Controllers;

use App\DAO\FormatoM8DAO;
use App\Entity\FormatoM8;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class FormatoM8Controller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    public function allPersonas(Request $req)
    {
        $reglas = ["nombre" => "required"];
        $this->validate($req, $reglas);
        try {
            $data = FormatoM8DAO::allPersonas($req->input('nombre'));
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function findPersonaById($id)
    {
        try {
            $data = FormatoM8DAO::findPersonaById($id);
            return response()->json($data);
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function registrarFormatoM8(Request $req)
    {
        $reglas = [
            "cve_usuario" => "required",
            // "descripcion" => "required",
            "fecha_registro" => "required",
            "tipo_formato" => "required"
        ];

        $this->validate($req, $reglas);
        $cve_colaborador = $req->get("id_colaborador");

        try {
            $name = time();
            $filename = "";
            if ($req->hasFile('archivo')) {
                $file = $req->file('archivo');
                $temp = explode(".", $file->getClientOriginalName());

                $directorio = '../upload/';
                $filename = $name . "." . $file->extension();
                if ($file->isValid()) {
                    try {
                        $file->move($directorio, $filename);
                    } catch (\Exception $e) {
                        return $e;
                    }
                }
            }


            $data = FormatoM8DAO::registrarFormatoM8((object)$req->all(), $cve_colaborador, $filename);
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getFormatoM8($id)
    {
        return FormatoM8DAO::getFormatoM8($id);
    }


    public function registrarRespuesta(Request $req, $id)
    {
        $reglas = [
            // "respuesta" => "required",
            "fecha_agrego" => "required",
            "estatus" => "required",
        ];

        $this->validate($req, $reglas);

        try {
            $data = FormatoM8DAO::registrarRespuesta($id, (object)$req->all());
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function uploadFile(Request $req, $id)
    {
        $name = time();
        if ($req->hasFile('archivo')) {
            $file = $req->file('archivo');
            $temp = explode(".", $file->getClientOriginalName());

            $directorio = '../upload/';
            $filename = $name . "." . $file->extension();
            if ($file->isValid()) {
                try {
                    $file->move($directorio, $filename);
                    FormatoM8DAO::saveFileName($id, $filename);
                    return $filename;
                } catch (\Exception $e) {
                    return $e;
                }
            } else return 'ocurrio un error con la foto ';
        } else {
            return 'no existe el Documento..';
        }
    }

    public function getTiposFormtoM8()
    {
        return FormatoM8DAO::getTiposFormtoM8();
    }
}
