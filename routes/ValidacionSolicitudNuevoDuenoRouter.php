<?php
$router->group(['prefix'=>'validacion-solicitud-nuevo-dueno','middleware' => 'auth'],function() use($router){    
    $router->get('','ValidacionSolicitudNuevoDuenoController@getSociosIngresoNuevoAll');
    $router->get('/{id:[0-9]+}','ValidacionSolicitudNuevoDuenoController@getSociosIngresoNuevo');
    $router->get('/foto','ValidacionSolicitudNuevoDuenoController@getViewFoto');
    $router->put('','ValidacionSolicitudNuevoDuenoController@setValidacionUsiarioNuevoIngreso');    
});
