<?php

$router->group(['prefix'=>'reporte-formato-m8'],function() use($router){
    
    //buscas los formatos m8 para generar reporte
    $router->get('','ReporteFormatoM8Controller@getFormatoM8');
    $router->get('/documento-file','ReporteFormatoM8Controller@getDocumentoFile');
    $router->get('by-accion','ReporteFormatoM8Controller@getFormatoM8ByAccion');
    $router->get('by-accion-detalle','ReporteFormatoM8Controller@getFormatosM8ByAccionDetalle');
    $router->get('by-accion-detalle-archivo','ReporteFormatoM8Controller@previewImagen');

});