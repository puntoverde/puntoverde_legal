<?php

namespace App\Controllers;

use App\DAO\AccionistaDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class AccionistaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function getAccionistas()
    {
        return AccionistaDAO::getAccionistas();
    }

    public function getAccionista($id)
    {
        return AccionistaDAO::findAccionista($id);
    }

    public function setAccionista(Request $req)
    {
        $reglas = ["nombre" => "required", "fecha_nacimiento" => "required", "curp" => "required", "rfc" => "required"];

        $this->validate($req, $reglas);
        return AccionistaDAO::insertAccionista((object)$req->all());
    }


    public function updateAccionista($id, Request $req)
    {
        $reglas = ["nombre" => "required", "fecha_nacimiento" => "required", "curp" => "required", "rfc" => "required"];

        $this->validate($req, $reglas);
        return AccionistaDAO::updateAccionista($id, (object)$req->all());
    }

    public function accionistaChange(Request $req)
    {
        $reglas = ["cve_dueno" => "required", "cve_accion" => "required"];
        $this->validate($req, $reglas);
        return AccionistaDAO::CambiarDueno((object)$req->all());
    }

    public function getDocumentos($id,Request $req)
    {
        return AccionistaDAO::getDocumentos($id,(object)$req->all());
    }
   
    public function uploadFile(Request $req)
    {
        if ($req->hasFile('documento')) {
            $file = $req->file('documento');
            $temp = explode(".", $file->getClientOriginalName());
            $directorio='../portafolio/';
            $filename = round(microtime(true)) . '.' . end($temp);
            if ($file->isValid()) {
             try{$file->move($directorio,$filename);
            return $filename;
            }
             catch(\Exception $e){return $e;}
            }
            else return 'no cargo bien ';
        }
        else {
            return 'no existe el Documento..';
        }
        
    }

    public function saveDocumento($id,Request $req){
        return AccionistaDAO::setDocumento($id,(object)$req->all());
    }

    public function deleteDocumento($id,Request $req){
        AccionistaDAO::deleteDocumento($id,$req->input('cve_documento'));
        return response(null, 204);
    }

    public function getDocumentoFile(Request $req)
    {    
        // $file=$req->input('documento');
        //  $mime="";
        //  $extencion=pathinfo($file,PATHINFO_EXTENSION);
        //  if($extencion=="jpg" || $extencion=="png")$mime="image/png";
        //  else if($extencion=="pdf")$mime="application/pdf";
        //  else if($extencion=="word")$mime="application/vnd.openxmlformats-officedocument.wordprocessingml.document";
        //  else if($extencion=="excel")$mime="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
        //  $return_file =file_get_contents("../portafolio/$file", false, stream_context_create(['http' => ['ignore_errors' => true]]));    
        //  return response($return_file)->header("Content-type",$mime)->header("Content-Length",filesize("../portafolio/$file"));


         $file=$req->input('documento');
        //  $return_file =file_get_contents("../portafolio/$file", false, stream_context_create(['http' => ['ignore_errors' => true]]));    
         $return_file =file_get_contents("../portafolio/$file");  
         
        
         $fh = fopen('php://memory', 'w+b');
         fwrite($fh, $return_file);
         $mime = mime_content_type($fh);
         fclose($fh);


         return response($return_file)->header("Content-type",$mime)->header("Content-Length",filesize("../portafolio/$file"));
        
        
    }


    public function uploadFoto(Request $req)
    {
        if ($req->hasFile('foto')) {
            $file = $req->file('foto');
            $temp = explode(".", $file->getClientOriginalName());
            $directorio = '../upload/';
            $filename = $req->input('cve_dueno') . '.jpeg';
            if ($file->isValid()) {
                try {
                    $file->move($directorio, $filename);
                    return $filename;
                } catch (\Exception $e) {
                    return $e;
                }
            } else return 'ocurrio un error con la foto ';
        } else {
            return 'no existe el Documento..';
        }
    }

    public function getViewFoto(Request $req)
    {
        $foto = $req->input('foto');
        $img = file_get_contents("../upload/$foto");
        return response($img)->header('Content-type', 'image/png');
    }

    public function addFoto($id, Request $req)
    {
        $foto = $req->input('foto');
        return AccionistaDAO::addFoto($id, $foto);
    }
    
    public function deleteFoto($id)
    {        
        return AccionistaDAO::deleteFoto($id);
    }


    public function setAccionistaHistorico(Request $req)
    {
        $reglas = ["nombre" => "required", "fecha_nacimiento" => "required", "curp" => "required", "rfc" => "required"];

        $this->validate($req, $reglas);
        return AccionistaDAO::insertAccionistaHistorico((object)$req->all());
    }

    public function getLibroAccionistas()
    {
        return AccionistaDAO::getLibroAccionistas();
    }
    
    public function getLibroAccionistasHistorico($id)
    {
        return AccionistaDAO::getLibroAccionistasHistorico($id);
    }

}
