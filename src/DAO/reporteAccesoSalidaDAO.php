<?php
namespace App\DAO;
use Illuminate\Support\Facades\DB;

class reporteAccesoSalidaDAO {

    public function __construct(){}

   public static function otorgarSalidas()
    {

        try {
            
            //obtiene promedio por tiempo que llevan los socios
            $hora_promedio=DB::table('acceso_socio')
            ->selectRaw('SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(salida,entrada)))) AS promedio_horas')
            ->whereNotNull('salida')
            ->whereRaw('fecha =CURDATE()')
            ->value('promedio_horas');

            //obtener fecha limite que es 10 pm menos el promedio
            $hora_limite=DB::select("SELECT TIMEDIFF('16:00:00',?) AS time_limite",[$hora_promedio]);

            DB::update("UPDATE acceso_socio SET salida= ADDTIME(entrada,?) WHERE fecha=CURDATE() AND salida IS NULL AND entrada <= ?;",[$hora_promedio,$hora_limite[0]->time_limite]);
            DB::update("UPDATE acceso_socio SET salida= '20:00:00' WHERE fecha=CURDATE() AND salida IS NULL AND entrada >= ?;",[$hora_limite[0]->time_limite]);
             
            return ["promedio"=>$hora_promedio, "limite"=>$hora_limite[0]->time_limite];
            
        } catch (\Exception $e) {
            return $e;
        } finally {
            
        }
    }

}