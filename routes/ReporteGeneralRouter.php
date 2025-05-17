<?php
$router->group(['prefix'=>'reporte-general'],function() use($router){
    
    $router->get('','ReporteGeneralController@getReporteGeneral');
    $router->get('/detalle','ReporteGeneralController@getReporteGeneralDetalle');
    $router->get('/detalle-anteriores','ReporteGeneralController@getReporteGeneralCargosAnterioresDetalle');
    $router->get('/detalle-anteriores-faltantes','ReporteGeneralController@getReporteGeneralCargosAnterioresFaltantesDetalle');
    $router->get('/detalle-accion2or3','ReporteGeneralController@getReporteGeneralAccionCargos2or3Detalle');
    $router->get('/detalle-accion-movimientos','ReporteGeneralController@getReporteGeneralMovimeintosAccionDetalle');
    $router->get('/detalle-accion','ReporteGeneralController@getAccionesByEstatus');
    $router->get('/detalle-socios-activos','ReporteGeneralController@getSociosActivosDetalle');
    $router->get('/detalle-socios-movimientos','ReporteGeneralController@getSociosMovimientosDetalle');
    $router->get('/historico-resumen-acciones','ReporteGeneralController@getHistoricoResumenAcciones');
    
});