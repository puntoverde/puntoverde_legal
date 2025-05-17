<?php

$router->group(['prefix'=>'reporte-historico-accion'],function() use($router){
    $router->get('','ReporteHistoricoAccionesController@getAccion');
    $router->get('/{id:[0-9]+}','ReporteHistoricoAccionesController@getDuenos');
    $router->get('/socios/{id:[0-9]+}','ReporteHistoricoAccionesController@getSocios');
    $router->get('/cargos/{id:[0-9]+}','ReporteHistoricoAccionesController@getCargos');    
    $router->get('/estatus_tipo/{id:[0-9]+}','ReporteHistoricoAccionesController@getTipoAndEstatus');    
});