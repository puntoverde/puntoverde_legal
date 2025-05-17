<?php
namespace App\DAO;
use App\Entity\Socio;
use App\Entity\Persona;
use App\Entity\Colonia;
use App\Entity\Direccion;
use App\Entity\Accion;
use App\Entity\Profesion;
use App\Entity\Parentesco;
use App\Entity\Cargo;
use App\Entity\Invitado;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvitadoDAO {

    public function __construct(){}
    /**
     * 
     */
    public static function insertInvitado($p)
    {   
        

       return DB::transaction(function () use ($p){
        try{
        $colonia=Colonia::find($p->colonia);
        $profesion=Profesion::find($p->profesion);
        // $parentesco=Parentesco::find($p->parentesco);
        $parentesco=Parentesco::find(12);
        $accion=Accion::find($p->cve_accion);

        $persona=new Persona();
        $persona->nombre=$p->nombre;
        $persona->apellido_paterno=$p->paterno;
        $persona->apellido_materno=$p->materno;
        $persona->sexo=$p->genero;
        $persona->fecha_nacimiento=$p->fecha_nac;
        $persona->cve_pais=$p->nacionalidad;
        // $persona->curp="NA";
        // $persona->rfc="NA";
        $persona->estado_civil=$p->estado_civil;
        $persona->estatus=1;
        $persona->save();

        $direccion=new Direccion();
        $direccion->calle=$p->calle;
        $direccion->numero_exterior=$p->num_ext;
        $direccion->numero_interior=$p->num_int;
        $direccion->colonia()->associate($colonia);
        $direccion->save();

        $posicion=Socio::where('cve_accion',$p->cve_accion)->selectRaw("COUNT(cve_socio)+1 AS posicion")->value('posicion');
      
        $socio=new Socio();
        $socio->posicion=$posicion;
        $socio->celular=$p->celular;
        // $socio->telefono=$p->telefono;
        $socio->correo_electronico=$p->correo;
        $socio->grado_estudio=$p->grado_estudio;
        $socio->estatus=1;
        
        $socio->persona()->associate($persona);
        $socio->direccion()->associate($direccion);
        $socio->profesion()->associate($profesion);
        $socio->parentesco()->associate($parentesco);
        $socio->accion()->associate($accion);
        $socio->save();    

        if($p->tipo_invitado==0)// cuando sea asi mismo se cargara ala accion de invitado
        {
            $p->cve_accion_cargo= $p->cve_accion;
            $p->cve_persona_cargo=$persona->cve_persona;
        }
        

        DB::select("INSERT into cargo(cve_accion, cve_cuota, cve_persona, concepto, total, subtotal, iva, cantidad, periodo, responsable_carga, fecha_cargo, recargo, estatus) 
        select ?,cve_cuota, ?, descripcion, ?, ?,?, 1, ?, 0, now(), 0, 1 from cuota where cve_cuota=?",
        [$p->cve_accion_cargo,$p->cve_persona_cargo,$p->total,($p->total/116)*100,(($p->total/116)*100)*.16,date("m-Y"),1007]);//1007
        
        $id_cargo=DB::getPdo()->lastInsertId();
        
        $invitado=new Invitado();
        $invitado->cve_socio=$socio->cve_socio;
        $invitado->fecha_inicio=$p->fecha_inicio;
        $invitado->fecha_fin=$p->fecha_fin;
        $invitado->cve_cargo=$id_cargo;
        $invitado->socio_invita=$p->cve_persona_cargo;//se cambia por el cve_persona por si invita socio o dueño
        $invitado->fecha_registro=Carbon::now();
        $invitado->save();

        if($p->tipo_acceso==0){
        $dias=array_map(function($i)use($invitado){return ["invitado"=>$invitado->id,"dia"=>$i,"estatus"=>1];},$p->dias_acceso);
        DB::table("historico_invitado_socio_dias")->insert($dias);
        }

        return 1;
        }
        catch(\PDOException $e)
        {
            return $e->getMessage();
        }        

        });

    }

    public static function reingresoInvitado($id,$p){
       
        return DB::transaction(function () use ($id,$p){
        try{
        
        if($p->tipo_invitado==0)// cuando sea asi mismo se cargara ala accion de invitado
        {
            $p->cve_accion_cargo= $p->cve_accion_cargo;
            $p->cve_persona_cargo=$p->cve_persona_cargo;
        }

        DB::select("INSERT into cargo(cve_accion, cve_cuota, cve_persona, concepto, total, subtotal, iva, cantidad, periodo, responsable_carga, fecha_cargo, recargo, estatus) 
        select ?,cve_cuota, ?, descripcion, ?, ?,?, 1, ?, 0, now(), 0, 1 from cuota where cve_cuota=?",
        [$p->cve_accion_cargo,$p->cve_persona_cargo,$p->total,($p->total/116)*100,(($p->total/116)*100)*.16,date("m-Y"),1007]);//1007
        
        $id_cargo=DB::getPdo()->lastInsertId();
        
        $invitado=new Invitado();
        $invitado->cve_socio=$id;
        $invitado->fecha_inicio=$p->fecha_inicio;
        $invitado->fecha_fin=$p->fecha_fin;
        $invitado->cve_cargo=$id_cargo;
        $invitado->socio_invita=$p->cve_persona_cargo;//se cambia por el cve_persona por si invita socio o dueño
        $invitado->save();

        if($p->tipo_acceso==0){
            $dias=array_map(function($i)use($invitado){return ["invitado"=>$invitado->id,"dia"=>$i,"estatus"=>1];},$p->dias_acceso);
            DB::table("historico_invitado_socio_dias")->insert($dias);
            }
            
        return 1;

         }
         catch(\PDOException $e)
         {

             return $e->getMessage();
         }
    
    
        });
        
    }

    public static function getInvitados($invitado){
       $invitados=Socio::join('persona','socios.cve_persona','persona.cve_persona')
        ->join('historico_invitado_socio','socios.cve_socio','historico_invitado_socio.cve_socio')
        ->join('acciones','socios.cve_accion','acciones.cve_accion')
        ->leftJoin('persona As persona2','historico_invitado_socio.socio_invita','persona2.cve_persona')
        ->leftJoin('socios As socios2','persona.cve_persona','socios2.cve_persona')
        ->leftJoin('historico_invitado_socio_dias','historico_invitado_socio.id','historico_invitado_socio_dias.invitado')

        ->select('socios.cve_socio AS id','socios2.cve_persona','socios2.cve_accion','socios.posicion','acciones.numero_accion')
        ->addSelect('persona.nombre','persona.apellido_paterno','persona.apellido_materno')
        ->addSelect('historico_invitado_socio.fecha_inicio','historico_invitado_socio.fecha_fin')
        ->addSelect(DB::raw("(SUM(CURDATE() BETWEEN historico_invitado_socio.fecha_inicio AND historico_invitado_socio.fecha_fin) + IFNULL( (CURDATE() <= MAX(historico_invitado_socio_dias.dia)),0)) > 0 AS estatus"))
        ->groupBy('socios.cve_socio')
        ->orderBy('persona.nombre')
        ->orderBy('socios.cve_socio')
        ->orderBy('historico_invitado_socio.fecha_fin','DESC')
        ->where('acciones.cve_tipo_accion',7) ;

        if($invitado??false)$invitados->whereRaw("CONCAT_WS(' ',persona.nombre,persona.apellido_paterno,persona.apellido_materno) LIKE ?",['%'.$invitado.'%']);
        
        return $invitados->get();  
    }
    
    public static function getInvitadoById($id){
        return Socio::join('persona','socios.cve_persona','persona.cve_persona')
        ->join('direccion' , 'socios.cve_direccion','direccion.cve_direccion')
        ->join('colonia' , 'direccion.cve_colonia' , 'colonia.cve_colonia')
        ->join('pais' , 'persona.cve_pais' , 'pais.cve_pais')
        ->join('profesion' , 'socios.cve_profesion','profesion.cve_profesion')
        ->join('parentescos' , 'socios.cve_parentesco','parentescos.cve_parentesco')
        ->select('persona.nombre','apellido_paterno','apellido_materno','persona.sexo','fecha_nacimiento')
        ->addSelect('pais.nombre AS nacionalidad','estado_civil','celular','numero_exterior','numero_interior') 
        ->addSelect('cp','colonia.nombre AS colonia','correo_electronico','grado_estudio','profesion.nombre AS profesion')
        ->addSelect('estado_accion')
        ->where('socios.cve_socio',$id)->first();      
    }


    public static function getHistoricoInvitado($id)
    {
      return DB::table('historico_invitado_socio')
      ->join('persona','historico_invitado_socio.socio_invita','persona.cve_persona')
      ->join('socios','persona.cve_persona','socios.cve_persona')
      ->join('acciones','socios.cve_accion','acciones.cve_accion')
      ->join('cargo','historico_invitado_socio.cve_cargo','cargo.cve_cargo')
      ->leftJoin('descuento','cargo.cve_cargo','descuento.cve_cargo')
      ->leftJoin('pago','cargo.idpago','pago.idpago')
      ->select('nombre','apellido_paterno','apellido_materno')
      ->addSelect('historico_invitado_socio.fecha_inicio','historico_invitado_socio.fecha_fin')
      ->addSelect('historico_invitado_socio.fecha_registro','pago.folio','pago.fecha_hora_cobro')
      ->addSelect(DB::raw("(cargo.total - IFNULL(descuento.monto,0)) AS total"))
      ->addSelect(DB::raw("CONCAT(numero_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END) AS accion"))
      ->where('historico_invitado_socio.cve_socio',$id)
      ->get();
    }

    public static function getSociosInvitaByNombre($nombre)
    {
      return Socio::join('persona','socios.cve_persona','persona.cve_persona')
      ->join('acciones','socios.cve_accion','acciones.cve_accion')
      ->select('cve_socio','acciones.cve_accion','persona.cve_persona')
      ->addSelect(DB::raw("CONCAT(numerao_accion,CASE clasificacion WHEN 1 THEN 'A' WHEN 2 THEN 'B' WHEN 3 THEN 'C' ELSE '' END,'-',posicion) AS nip"))
      ->addSelect(DB::raw("CONCAT_WS(' ',nombre,apellido_paterno,apellido_materno) AS socio"))
      ->whereRaw("CONCAT_WS(' ',nombre,apellido_paterno,apellido_materno) LIKE ?",['%'.$nombre.'%'])
      ->where('numero_accion','<',1500)
      ->get();
    }


    public static function getListaSociosInvitan($numero_accion,$clasificacion)
    {
      $socios=Socio::join('acciones','socios.cve_accion','acciones.cve_accion')
      ->join('persona','socios.cve_persona','persona.cve_persona')
      ->select('cve_socio','acciones.cve_accion','persona.cve_persona')
      ->addSelect('nombre','apellido_paterno','apellido_materno',DB::raw("1 as socio"))
      ->where('numero_accion',$numero_accion)
      ->where('clasificacion',$clasificacion);

      $duenos=DB::table('acciones')
      ->join('dueno','acciones.cve_dueno','dueno.cve_dueno')
      ->join('persona','dueno.cve_persona','persona.cve_persona')
      ->leftJoin('socios','persona.cve_persona','socios.cve_persona')
      ->select('socios.cve_socio','acciones.cve_accion','persona.cve_persona')
      ->addSelect('nombre','apellido_paterno','apellido_materno',DB::raw("0 as socio"))
      ->where('numero_accion',$numero_accion)
      ->where('clasificacion',$clasificacion);

      return $socios->union($duenos)->get();

    }

    public static function getAccionesLibresInvitados()
    {
        return Accion::leftJoin('socios','acciones.cve_accion','socios.cve_accion')
        ->select('acciones.cve_accion','numero_accion')
        ->where('cve_tipo_accion',7)
        ->groupBy('acciones.cve_accion')
        ->havingRaw('COUNT(socios.cve_socio) < ?', [20])
        ->get();
    }

    public static function getInvitadosCargos()
    {
        // SELECT 
        //     historico_invitado_socio.cve_socio,
	    //     historico_invitado_socio.id,
	    //     CONCAT(acciones.numero_accion,case clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' ELSE '' END) AS accion,
	    //     socios.posicion,
	    //     persona.nombre,
	    //     persona.apellido_paterno,
	    //     persona.apellido_materno,
	    //     persona.sexo,
	    //     persona.fecha_nacimiento,
	    //     persona.estado_civil,
	    //     socios.celular,
	    //     socios.correo_electronico,
	    //     historico_invitado_socio.fecha_inicio,
	    //     historico_invitado_socio.fecha_fin,
	    //     historico_invitado_socio.fecha_registro,
	    //     cargo.total,
	    //     IF(cargo.idpago IS NULL,0,1) AS pagado,
	    //     (CURDATE() > historico_invitado_socio.fecha_fin AND cargo.idpago IS NULL) AS pasado
	    //     FROM cargo 
        // INNER JOIN historico_invitado_socio ON cargo.cve_cargo=historico_invitado_socio.cve_cargo
        // INNER JOIN socios ON historico_invitado_socio.cve_socio=socios.cve_socio
        // INNER JOIN persona ON socios.cve_persona=persona.cve_persona
        // INNER JOIN acciones ON socios.cve_accion=acciones.cve_accion
        // WHERE cve_cuota=1007 
        // AND cargo.estatus=1
        // AND (CURDATE() BETWEEN historico_invitado_socio.fecha_inicio AND historico_invitado_socio.fecha_fin OR CURDATE()< historico_invitado_socio.fecha_fin)
        // OR (CURDATE() > historico_invitado_socio.fecha_fin AND cargo.idpago IS NULL)
        // ORDER BY fecha_cargo DESC	

        return DB::table("cargo")
        ->join("historico_invitado_socio" , "cargo.cve_cargo","historico_invitado_socio.cve_cargo")
        ->join("socios" , "historico_invitado_socio.cve_socio","socios.cve_socio")
        ->join("persona" , "socios.cve_persona","persona.cve_persona")
        ->join("acciones" , "socios.cve_accion","acciones.cve_accion")
        ->where("cve_cuota",1007)
        ->where("cargo.estatus",1)
        ->where(function($where){$where->whereRaw("CURDATE() BETWEEN historico_invitado_socio.fecha_inicio AND historico_invitado_socio.fecha_fin")->orWhereRaw("CURDATE()< historico_invitado_socio.fecha_fin");})
        ->orWhere(function($where){$where->whereRaw("CURDATE() > historico_invitado_socio.fecha_fin")->whereNull("cargo.idpago");})
        ->select(  
                "historico_invitado_socio.cve_socio",
	            "historico_invitado_socio.id",          
                "socios.posicion",
                "persona.nombre",
                "persona.apellido_paterno",
                "persona.apellido_materno",
                "persona.sexo",
                "persona.fecha_nacimiento",
                "persona.estado_civil",
                "socios.celular",
                "socios.correo_electronico",
                "historico_invitado_socio.fecha_inicio",
                "historico_invitado_socio.fecha_fin",
                "historico_invitado_socio.fecha_registro",
                "cargo.total"
        )
        ->selectRaw("CONCAT(acciones.numero_accion,case clasificacion when 1 then 'A' when 2 then 'B' when 3 then 'C' ELSE '' END) AS accion")
        ->selectRaw("IF(cargo.idpago IS NULL,0,1) AS pagado")
        ->selectRaw("(CURDATE() > historico_invitado_socio.fecha_fin AND cargo.idpago IS NULL) AS pasado")
        ->orderBy("persona.nombre")
        ->orderBy("persona.apellido_paterno")
        // ->orderBy("fecha_cargo","desc")
        ->get();
        
    }

    public static function deleteInvitado($id_socio,$id_invitado)
    {
       return DB::transaction(function()use($id_socio,$id_invitado){
        
        //solo eliminara invitados que no tengan el cargo pagado y el invitado lo pasara a la tabla invitados_eliminados(aun no creo la tabla)
        //igual tengo que considerar si es un reingreso lo que se va a eliminar ejemplo juan realizo un reingreso es un solo invitado pero con dos filas en historico_invitado socios 
        //y si quiero eliminar solo se elimina el registro del historico y este tambien se guarda en una tabla(que aun no genero)

        /*
        SELECT IF(cargo.idpago IS NULL,1,0),cargo.idpago FROM historico_invitado_socio
        INNER JOIN cargo  ON historico_invitado_socio.cve_cargo=cargo.cve_cargo
        WHERE historico_invitado_socio.id=478
         */

        //indica si se puede eliminar en base al idpago 1 es que no esta pagado deja eliminar 0 esta pagado osea no deja eliminar
        $flag_elimina=DB::table("historico_invitado_socio")->join("cargo","historico_invitado_socio.cve_cargo","cargo.cve_cargo")->where("historico_invitado_socio.id",$id_invitado)->selectRaw("IF(cargo.idpago IS NULL,1,0) as fla")->value("fla");
        // dd($flag_elimina);
        /*
        SELECT COUNT(historico_invitado_socio.id) 
        FROM historico_invitado_socio        
        WHERE historico_invitado_socio.cve_socio=12332
        */

        //indica si tiene mas de un registro en historico si tiene un reingreso serian 2 el registro original y el reingreso
        $elimina_si_unico=DB::table("historico_invitado_socio")->where("cve_socio",$id_socio)->count();
        // dd($elimina_si_unico);

        //como solo tiene un registro en historico_socio_invitado y este no esta pagado(osea 1), se elimina todo el socio ->persona->direccion->historico invitados 
        if($elimina_si_unico==1 && $flag_elimina==1)
        {
            // 1.-obtiene socio
            // 2.-guarda socio respaldo
            // 3.-elimina socio
            // 4.-elimina historico
            $socio_eliminar=DB::table("socios")
            ->select("cve_socio","cve_persona","cve_direccion","cve_profesion","cve_parentesco","cve_accion","celular","correo_electronico","posicion",DB::raw("0 AS temporal"))
            ->where("cve_socio",$id_socio)->first();
            // dd($socio_eliminar);
            $persona_eliminar=DB::table("persona")->where("cve_persona",$socio_eliminar->cve_persona)->first();
            // dd($persona_eliminar);
            $direccion_eliminar=DB::table("direccion")->where("cve_direccion",$socio_eliminar->cve_direccion)->first();
            // dd($direccion_eliminar);
            $historico_eliminar=DB::table("historico_invitado_socio")->where("id",$id_invitado)->first();
            // dd($historico_eliminar);
            $historico_eliminar_dias=DB::table("historico_invitado_socio_dias")->where("invitado",$id_invitado)->selectRaw("GROUP_CONCAT(dia) as dias")->first();
            // dd($historico_eliminar_dias);
            DB::statement("SET FOREIGN_KEY_CHECKS = 0;");
            DB::table("socios")->where("cve_socio",$id_socio)->delete();
            DB::table("direccion")->where("cve_direccion",$socio_eliminar->cve_direccion)->delete();
            DB::table("persona")->where("cve_persona",$socio_eliminar->cve_persona)->delete();
            DB::table("historico_invitado_socio_dias")->where("invitado",$id_invitado)->delete();
            DB::table("historico_invitado_socio")->where("id",$id_invitado)->delete();
            DB::statement("SET FOREIGN_KEY_CHECKS = 1;");

            // dd(collect($socio_eliminar)->merge($persona_eliminar)->merge($direccion_eliminar)->merge($historico_eliminar)->merge($historico_eliminar_dias));
            return DB::table("invitado_eliminar")->insert(collect($socio_eliminar)->merge($persona_eliminar)->merge($direccion_eliminar)->merge($historico_eliminar)->merge($historico_eliminar_dias)->toArray());                    

        }
        //tiene mas de un registro y el que se selecciono no esta pagado se elimina solo el historico no se toca al socio porq hay otro historico que lo respalda
        else if($elimina_si_unico > 1 && $flag_elimina==1)
        {
            // 1.-obtiene socio
            // 2.-guarda socio respaldo estatus temp
            // 4.-elimina historico
            $socio_temp=DB::table("socios")
            ->select("cve_socio","cve_persona","cve_direccion","cve_profesion","cve_parentesco","cve_accion","celular","correo_electronico","posicion",DB::raw("1 AS temporal"))
            ->where("cve_socio",$id_socio)->first();
            // dd($socio_eliminar);
            $persona_temp=DB::table("persona")->where("cve_persona",$socio_temp->cve_persona)->first();
            // dd($persona_eliminar);
            $direccion_temp=DB::table("direccion")->where("cve_direccion",$socio_temp->cve_direccion)->first();
            // dd($direccion_eliminar);
            $historico_eliminar=DB::table("historico_invitado_socio")->where("id",$id_invitado)->first();
            // dd($historico_eliminar);
            $historico_eliminar_dias=DB::table("historico_invitado_socio_dias")->where("invitado",$id_invitado)->selectRaw("GROUP_CONCAT(dia) as dias")->first();
            // dd($historico_eliminar_dias);

            DB::table("historico_invitado_socio_dias")->where("invitado",$id_invitado)->delete();
            DB::table("historico_invitado_socio")->where("id",$id_invitado)->delete();

            // dd(collect($socio_eliminar)->merge($persona_eliminar)->merge($direccion_eliminar)->merge($historico_eliminar)->merge($historico_eliminar_dias));
            return DB::table("invitado_eliminar")->insert(collect($socio_temp)->merge($persona_temp)->merge($direccion_temp)->merge($historico_eliminar)->merge($historico_eliminar_dias)->toArray());
        }
        else{
            return 0;
        }

    });//fin transaccion
    }

}