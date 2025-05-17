<?php

$router->group(['prefix'=>'lockers'],function() use($router){
    $router->get('','LockerController@getLockers');
    $router->get('/{id:[0-9]+}','LockerController@getHistoricoLocker');
    $router->post('/{id:[0-9]+}','LockerController@asignarLocker');
    $router->delete('/{id:[0-9]+}','LockerController@cancelarRentaLocker');
    $router->get('/socios','LockerController@getSocios');
    $router->get('/cuota','LockerController@getCuota');
    $router->put('/contrato','LockerController@getCuota');
    $router->put('/cambio-estado/{id:[0-9]+}','LockerController@regularizadoNoRegularizado');
    $router->post('/upload-contrato','LockerController@uploadContrato');
    $router->put('/{id:[0-9]+}','LockerController@AsignarContrato');
    $router->get('/view-contrato','LockerController@viewContrato');
    $router->get('/edificios','LockerController@getEdificios');
        
    $router->get('/libres','LockerController@getListaLockerDisponibles'); 
    $router->post('/asignar-masivo','LockerController@asignarLockerMasivo');

    $router->put('/{id:[0-9]+}/asignar','LockerController@EditarAsignacionLocker');


    $router->get('/duenos','LockerController@getDuenos');
    $router->put('/{id:[0-9]+}/dueno','LockerController@EditarDuenoLocker');
    
    
    $router->put('/{id:[0-9]+}/numero_locker','LockerController@EditarUltimoNumeroLocker');

    $router->put('/{id:[0-9]+}/observaciones','LockerController@EditarObservacionAsignacion');
    
    $router->post('/permuta','LockerController@agregar_permuta');

    //para cuando se corta un candado en un locker
    $router->post('/liberar','LockerController@liberaLocker');
    $router->get('/liberar-historico/{id:[0-9]+}','LockerController@historicoLiberacion');
    $router->get('/reporte-liberar-historico','LockerController@reporteHistoricoLiberacion');

    //para crear locker y actulizar locker
    $router->post('','LockerController@crearLocker');
    $router->put('/{id:[0-9]+}/actualizar','LockerController@ModificarLocker');
    $router->get('/lista-locker','LockerController@getListaLockers');
    $router->get('/find-locker/{id:[0-9]+}','LockerController@getLockerById');

});