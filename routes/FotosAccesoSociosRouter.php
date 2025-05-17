<?php

$router->group(['prefix'=>'fotos-acceso-socios'],function() use($router){
    //crear fotos socios
    $router->post('','FotosAccesoSociosController@crearFotoAcceso');
    //actualizar fotos socios
    $router->put('/{id:[0-9]+}','FotosAccesoSociosController@getAccionById');
    //recuperar la foto del socio 
    $router->get('/{id:[0-9]+}','FotosAccesoSociosController@getFotoAcceso');
    
});