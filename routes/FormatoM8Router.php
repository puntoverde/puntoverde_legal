<?php

$router->group(['prefix'=>'formato-m8','middleware' => 'auth'],function() use($router){
    
    //busca las personas o socios???
    $router->get('/personas','FormatoM8Controller@allPersonas');
    
    //detalle de la persona realizar el examen medico..
    $router->get('/persona/{id:[0-9]+}','FormatoM8Controller@findPersonaById');

    //obtiene todos los formatos m8 de una persona
    $router->get('/{id:[0-9]+}','FormatoM8Controller@getFormatoM8');
    //registra formato m8..
    $router->post('','FormatoM8Controller@registrarFormatoM8');

    $router->put('/respuesta/{id:[0-9]+}','FormatoM8Controller@registrarRespuesta');
    
    $router->post('/upload-file/{id:[0-9]+}','FormatoM8Controller@uploadFile');

    $router->get('/tipo-formato','FormatoM8Controller@getTiposFormtoM8');

});