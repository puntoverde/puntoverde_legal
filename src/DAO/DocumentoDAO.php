<?php

namespace App\DAO;

use App\Entity\Documento;
use Illuminate\Support\Facades\DB;


class DocumentoDAO
{

    public function __construct()
    {
    }

    public static function getDocumentos()
    {
        return Documento::all();
    }

    public static function getDocumento($id)
    {
        return Documento::leftJoin("documento_proceso","documento.cve_documento","documento_proceso.cve_documento")
        ->where('documento.cve_documento',$id)
        ->select('documento',DB::raw('CAST(tipo AS UNSIGNED) AS tipo'),DB::raw("GROUP_CONCAT(proceso) AS procesos"))
        ->first();
    }

    public static function setDocumento($p)
    {
        DB::transaction(function() use ($p){
            
            $documento = new Documento();
            $documento->documento = $p->documento;
            $documento->tipo = $p->tipo;
            $documento->estatus = 1;
            $documento->save();

            $procesos=array_map(function($i) use($documento){return ["cve_documento"=>$documento->cve_documento,"proceso"=>$i,"estatus"=>1];},$p->procesos);

            DB::table("documento_proceso")->insert($procesos);

        });

        
    }

    public static function updateDocumento($id, $p)
    {

        DB::transaction(function() use ($id,$p){

        $documento = Documento::find($id);
        $documento->documento = $p->documento;
        $documento->tipo = $p->tipo;
        $documento->save();

        DB::table("documento_proceso")->where("cve_documento",$id)->delete();

        $procesos=array_map(function($i) use($documento){return ["cve_documento"=>$documento->cve_documento,"proceso"=>$i,"estatus"=>1];},$p->procesos);

        DB::table("documento_proceso")->insert($procesos);

    });

    }

    public static function deleteDocumentos($id)
    {
        $documento = Documento::find($id);
        $documento->estatus = 0;
        $documento->save();
    }
}
