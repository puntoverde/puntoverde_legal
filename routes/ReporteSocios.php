<?php
$router->group(['prefix'=>'reporte-socios'],function() use($router){
    
    $router->get('','ReporteSociosController@getListaSocios');

    $router->get('/{id:[0-9]+}','ReporteSociosController@getCargarDetalles');

    $router->get('/cargo/{id:[0-9]+}','ReporteSociosController@getCargo');

    $router->put('/{id:[0-9]+}','ReporteSociosController@activarSocio');
    
    $router->get('/foto','ReporteSociosController@getFotoSocio');

    $router->post('/{id:[0-9]+}/upload-foto','ReporteSociosController@uploadFotoSocio');
    
});

