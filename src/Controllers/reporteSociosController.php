<?php
namespace App\Controllers;
use App\DAO\reporteSociosDAO;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class ReporteSociosController extends Controller
{

    public function __construct(){}

    public function getListaSocios(Request $req){            
        // return response()->json(reporteSociosDAO::listaSocios((object)$req->all()))->setEncodingOptions(JSON_NUMERIC_CHECK);
        return reporteSociosDAO::listaSocios((object)$req->all());
    }

    public function getCargarDetalles($id){

        return reporteSociosDAO::cargarDetalles($id);
    }

    public function getCargo($id){
        return json_encode(reporteSociosDAO::cargarAdeudos($id));
    }

    public function activarSocio($id, Request $req){
        return json_encode(reporteSociosDAO::toggleSocio($id, (object)$req->all()));
    }

    public function getFotoSocio(Request $req)
    {
        $cve_socio=$req->input("foto");
        $img= reporteSociosDAO::getFotoSocio($cve_socio);                            
        return response($img)->header('Content-type','image/png');
    }

    public function uploadFotoSocio(Request $req,$id)
    {
        if ($req->hasFile('foto')) {
            $file = $req->file('foto');
            $img= file_get_contents($file);
            return reporteSociosDAO::updateFotoSocio($id,$img);
            // return response($img)->header('Content-type','image/png');
            // return $file;

            
            // $temp = explode(".", $file->getClientOriginalName());
            // $directorio='../upload/';
            // $filename = round(microtime(true)) . '.' . end($temp);
            // if ($file->isValid()) {
            //  try{
                 
                // $file->move($directorio,$filename);
                // reporteSociosDAO::updateFotoSocio($id,$filename);
              
            // return $filename;
            // }
            //  catch(\Exception $e){return $e;}
            // }
            // else return 'no cargo bien ';
        }
        else {
            return 'no existe el Documento..';
        }
        
    }

}




