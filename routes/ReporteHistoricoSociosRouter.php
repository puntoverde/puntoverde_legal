<?php

$router->group(['prefix'=>'reporte-historico-socios'],function() use($router){
    $router->get('','ReporteHistoricoSociosController@getSocio');
    $router->get('/{id:[0-9]+}','ReporteHistoricoSociosController@getHistorico');
    $router->get('/datos/{id:[0-9]+}','ReporteHistoricoSociosController@getSocioDatos');
    $router->post('','ReporteHistoricoSociosController@setHistorico');
});