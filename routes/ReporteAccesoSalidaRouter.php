<?php

$router->group(['prefix'=>'reporte-acceso-salida'],function() use($router){
    $router->get('','reporteAccesoSalidaController@otorgarSalidas');
});