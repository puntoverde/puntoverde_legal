<?php
namespace App\DAO;
use App\Entity\Parentesco;
use Illuminate\Support\Facades\DB;


class ParentescoDAO {

    public function __construct(){}

    public static function getParentescos(){
         return Parentesco::all();
   }
    
   public static function getParentescosByTipoAccion($id_accion){

      //    return Parentesco::join("acciones","parentescos.cve_tipo_accion","acciones.cve_tipo_accion")
      //    ->where("cve_accion",$id_accion)
      //    ->select("parentescos.cve_parentesco","parentescos.cve_tipo_accion","parentescos.nombre","parentescos.sexo","parentescos.estatus")->get();
      return Parentesco::whereIn("cve_tipo_accion",[2,3])->select("parentescos.cve_parentesco","parentescos.cve_tipo_accion","parentescos.nombre","parentescos.sexo","parentescos.estatus")->get();
   }

}