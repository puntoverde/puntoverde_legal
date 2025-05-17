<?php
$router->group(['prefix'=>'descuento'],function() use($router){
    
    $router->get('','DescuentoController@getDescuento');

    $router->delete('/{cve}','DescuentoController@getDeleteDescuento');

    $router->get('/reporte','DescuentoController@getDeleteDescuentoReporte');

    
});

