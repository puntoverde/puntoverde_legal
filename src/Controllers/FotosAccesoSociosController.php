<?php

namespace App\Controllers;

use App\DAO\FotosAccesoSociosDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class FotosAccesoSociosController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function crearFotoAcceso(Request $req)
    {

        try {
            //crear la carpeta para un usuario
            $directorio = '../fotos_acceso_socios/socio_3';
            $filename = date('YmdHis');

            if (!is_dir($directorio)) mkdir($directorio, 0777, true);

            if (!$req->hasFile('foto')) return 'no existe el Documento..';

            $file = $req->file('foto');

            if (!$file->isValid()) return 'archivo dañado';

            $file->move($directorio, "$filename.png");
            $cve_socio=$req->input("cve_socio");
            
            return FotosAccesoSociosDAO::crearFotoAcceso($cve_socio,"$filename.png");
        } catch (\Exception $e) {
            return $e;
        }


    }

    public function actualizarFotoAcceso($id)
    {

       
    }


    public function getFotoAcceso($id)
    {
        $foto_name= FotosAccesoSociosDAO::getFotoAcceso($id);        
        $img = file_get_contents("../fotos_acceso_socios/socio_3/$foto_name");
        return response($img)->header('Content-type', 'image/png');
    }
}
