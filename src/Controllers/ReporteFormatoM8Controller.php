<?php

namespace App\Controllers;

use App\DAO\ReporteFormatoM8DAO;
use App\Entity\FormatoM8;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class ReporteFormatoM8Controller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function getFormatoM8(Request $req)
    {
        // $reglas = ["nombre" => "required"];
        // $this->validate($req, $reglas);
  
        try {
            $data = ReporteFormatoM8DAO::getFormatoM8((object)$req->all());
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getDocumentoFile(Request $req)
    {   
         $file=$req->input('documento');
         $mime="";
         $extencion=pathinfo($file,PATHINFO_EXTENSION);         
         if($extencion=="jpg")$mime="image/jpg";
         else if($extencion=="jpeg")$mime="image/jpeg";
         else if($extencion=="png")$mime="image/png";
         else if($extencion=="svg")$mime="image/svg";
         else if($extencion=="pdf")$mime="application/pdf";
         else if($extencion=="html")$mime="application/xml";
         else if($extencion=="xml")$mime="application/xml";
         else if($extencion=="word")$mime="application/vnd.openxmlformats-officedocument.wordprocessingml.document";
         else if($extencion=="docx")$mime="application/vnd.openxmlformats-officedocument.wordprocessingml.document";
         else if($extencion=="doc")$mime="application/msword";
         else if($extencion=="excel")$mime="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
         else if($extencion=="xlsx")$mime="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
         else if($extencion=="xls")$mime="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
         $return_file =file_get_contents("../upload/$file", false, stream_context_create(['http' => ['ignore_errors' => true]]));    
         return response($return_file)->header("Content-type",$mime)->header("Content-Length",filesize("../upload/$file"));
        // return response()->file("../portafolio/$file",['Content-Type' => 'image/png']);
    }

    public function getFormatoM8ByAccion()
    {
        try{
            return ReporteFormatoM8DAO::getFormatoM8ByAccion();
        }
        catch(\Exception $e)
        {

        }
    }

    public function getFormatosM8ByAccionDetalle(Request $req)
    {
        try{
            $cve_accion=$req->input("cve_accion");
            return ReporteFormatoM8DAO::getFormatosM8ByAccionDetalle($cve_accion);
        }
        catch(\Exception $e)
        {

        }
    }

    public function previewImagen(Request $req)
    {
        $archivo = $req->input('archivo');
        $img = file_get_contents("../upload/$archivo");
        return response($img)->header('Content-type', 'image/*');
    }


}
