<?php
$router->group(['prefix'=>'reporte-lockers'],function() use($router){
    $router->get('/edificios','ReporteLockerController@getEdificios');
    $router->get('','ReporteLockerController@getReporteLockers');
    $router->get('/totales','ReporteLockerController@getTotales');    
    $router->get('/totales-filter','ReporteLockerController@getReporteLockersFull');    

    //apartado de estadistica de lockers
    $router->get('/estadistica-cards','ReporteLockerController@getEstadisticasCards');
    $router->get('/estadistica-rentados','ReporteLockerController@getEstadisticaRentados');
    $router->get('/estadistica-norenueva','ReporteLockerController@getEstadisticaNoRenueva');
    $router->get('/estadistica-pertenece-club','ReporteLockerController@getEstadisticaPerteneceClub');
    $router->get('/estadistica-pertenece-externo','ReporteLockerController@getEstadisticaPerteneceExterno');
    $router->get('/estadistica-cargos-pagos','ReporteLockerController@getEstadisticaCargoOrPagos');
    $router->get('/estadistica-libres','ReporteLockerController@getEstadisticaLibres');


});