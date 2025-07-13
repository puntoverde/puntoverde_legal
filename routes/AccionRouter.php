<?php

$router->group(['prefix'=>'acciones'],function() use($router){
    $router->get('','AccionController@getAcciones');
    $router->get('/{id:[0-9]+}','AccionController@getAccionById');
    $router->put('/{id:[0-9]+}','AccionController@updateAccion');
    $router->post('/{id:[0-9]+}/add-activacion','AccionController@agregarCuotaActivacion');
    $router->put('/fechas_libro_accion',["uses"=>'AccionController@updateFechasAccion','middleware' => 'auth']);
});