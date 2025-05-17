<?php

namespace App\Middleware;

use Closure;
use App\Util\Auth;

class ExampleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // var_dump($request->header('Authorization'));
        $token = $request->header('Authorization');      
        $perfil = $request->header('perfil');      

       
        
        try{
            Auth::Check($token);
            $data=Auth::GetData(trim($token));              

            $request->attributes->add([
                "token"=>$data,
                "id_usuario"=>$data->id_usuario,
                "id_usuario_enmascarado"=>$data->id_usuario_enmascarado,
                "id_colaborador"=>$data->id_colaborador,
                "cve_persona"=>$data->cve_persona,
                "id_perfil"=>collect($data->perfiles)->contains("cve_perfil",$perfil)?$perfil:null,             
            ]);
            return $next($request);
        }
        catch(\Exception $ex){
            return response('Unauthorized:'.$ex,401);
        }
    }
}
