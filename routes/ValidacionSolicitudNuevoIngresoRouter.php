<?php
$router->group(['prefix'=>'validacion-solicitud-nuevo-ingreso'],function() use($router){    
    $router->get('','ValidacionSolicitudNuevoIngresoController@getSociosIngresoNuevoAll');
    $router->get('/{id:[0-9]+}','ValidacionSolicitudNuevoIngresoController@getSociosIngresoNuevo');
    $router->get('/foto','ValidacionSolicitudNuevoIngresoController@getViewFoto');
    $router->put('/{id:[0-9]+}','ValidacionSolicitudNuevoIngresoController@setValidacionUsiarioNuevoIngreso');    
});