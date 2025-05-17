<?php
$router->group(['prefix'=>'accionistas'],function() use($router){
    
    $router->get('','AccionistaController@getAccionistas');

    $router->get('/{id:[0-9]+}','AccionistaController@getAccionista');

    $router->post('','AccionistaController@setAccionista');

    $router->put('/{id:[0-9]+}','AccionistaController@updateAccionista');

    $router->put('/cambio','AccionistaController@accionistaChange');

    $router->post('/upload-foto','AccionistaController@uploadFoto');

    $router->get('/foto','AccionistaController@getViewFoto');

    $router->put('/{id:[0-9]+}/foto','AccionistaController@addFoto');
    
    $router->delete('/{id:[0-9]+}/foto','AccionistaController@deleteFoto');

    $router->get('/{id:[0-9]+}/documentos','AccionistaController@getDocumentos');

    $router->post('/upload-file','AccionistaController@uploadFile');

    $router->post('/{id:[0-9]+}/documento','AccionistaController@saveDocumento');

    $router->delete('/{id:[0-9]+}/documento','AccionistaController@deleteDocumento');

    $router->get('/{id:[0-9]+}/documento-file','AccionistaController@getDocumentoFile');

    $router->post('/historico','AccionistaController@setAccionistaHistorico');
    
    $router->get('/libro-accionista','AccionistaController@getLibroAccionistas');
    $router->get('/libro-accionista/{id:[0-9]+}','AccionistaController@getLibroAccionistasHistorico');
});